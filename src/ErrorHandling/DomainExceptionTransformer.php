<?php

declare(strict_types=1);

namespace App\ErrorHandling;

use App\Service\Exception\ConstraintViolation\SymfonyViolationAdapter;
use App\Service\Exception\DomainConflictException;
use App\Service\Exception\DomainConstraintViolationListException;
use App\Service\Exception\DomainException;
use App\Service\Exception\DomainExternalException;
use App\Service\Exception\DomainNotFoundException;
use App\Service\Exception\DomainValidationException;
use Phpro\ApiProblem\ApiProblemInterface;
use Phpro\ApiProblem\Http\HttpApiProblem;
use Phpro\ApiProblemBundle\Transformer\ExceptionTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final class DomainExceptionTransformer implements ExceptionTransformerInterface
{
    private const DEFAULT_TITLE = 'An error occurred';

    private const DEFAULT_DETAIL = 'Internal Server Error';

    private const STATUS_MAP = [
        DomainNotFoundException::class => Response::HTTP_BAD_REQUEST, // ресурс/сущность не найдены
        DomainConflictException::class => Response::HTTP_CONFLICT, // логический конфликт или дублирование
        DomainValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY, // не прошли бизнес-валидацию
        DomainConstraintViolationListException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        DomainExternalException::class => Response::HTTP_SERVICE_UNAVAILABLE, // внешние системы недоступны
//        DomainAccessDeniedException::class => Response::HTTP_FORBIDDEN, // запрещено доменной логикой
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly bool $debug,
    ) {
    }

    public function accepts(Throwable $exception): bool
    {
        return true;
    }

    /**
     * Все доменные ошибки унаследованны от нашего базового App\Service\Exception\DomainException
     */
    public function transform(Throwable $exception): ApiProblemInterface
    {
        if ($exception instanceof DomainException) {
            $status = self::mapDomainExceptionToStatusCode($exception);
            $title = $exception->getTitle();
            $detail = $exception->getDetail();
        } elseif ($exception instanceof HttpExceptionInterface) {
            $status = self::mapDomainExceptionToStatusCode($exception);
            $title = HttpApiProblem::getTitleForStatusCode($status);
            $detail = $exception->getMessage();
        } else {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $title = self::DEFAULT_TITLE;
            $detail = self::DEFAULT_DETAIL;
        }

        $problem = [
            'type'     => 'about:blank',
            'title'    => $title,
            'status'   => $status,
            'detail'   => $detail,
            'instance' => $this->requestStack->getCurrentRequest()?->getRequestUri(),
        ];

        $violationException = self::getValidationFailedException($exception);

        if (
            $violationException instanceof ValidationFailedException
            || $exception instanceof DomainConstraintViolationListException
        ) {
            $problem['violations'] = [];

            if ($violationException instanceof ValidationFailedException) {
                $problem['detail'] = 'One or more fields failed validation';

                foreach ($violationException->getViolations() as $violation) {
                    $adapter = new SymfonyViolationAdapter($violation);
                    $problem['violations'][] = [
                        'field' => $adapter->getField(),
                        'message' => $adapter->getMessage()
                    ];
                }
            }

            if ($exception instanceof DomainConstraintViolationListException) {
                foreach ($exception->getViolations() as $violation) {
                    $problem['violations'][] = [
                        'field' => $violation->getField(),
                        'message' => $violation->getMessage()
                    ];
                }
            }
        }

        if ($this->debug) {
            $problem['exception']['detail'] = $exception->getMessage();
            $problem['exception']['class'] = get_class($exception);
            $problem['exception']['line'] = $exception->getLine();
            $problem['exception']['file'] = $exception->getFile();
            $problem['exception']['trace'] = $exception->getTrace();
        }

        return new HttpApiProblem($status, $problem);
    }

    private static function mapDomainExceptionToStatusCode(Throwable $e): int
    {
        // map base class to correct code error
        foreach (self::STATUS_MAP as $class => $code) {
            if ($e instanceof $class) {
                return $code;
            }
        }

        return Response::HTTP_BAD_REQUEST; // common error
    }

    private static function getValidationFailedException(Throwable $exception): ?ValidationFailedException
    {
        if ($exception instanceof ValidationFailedException) {
            return $exception;
        }

        $previousException = $exception->getPrevious();

        if ($previousException instanceof ValidationFailedException) {
            return $previousException;
        }

        return null;
    }
}