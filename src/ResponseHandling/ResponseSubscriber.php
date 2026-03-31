<?php

declare(strict_types=1);

namespace App\ResponseHandling;

use App\ResponseHandling\ResponseCollection\ResponseCollectionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 0]
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        if (!$result instanceof ResponseCollectionInterface) {
            return;
        }

        $data = [
            'data' => $result->getItems(),
        ];

        if (!empty($result->getMeta())) {
            $data['meta'] = $result->getMeta();
        }

        if (!empty($result->getLinks())) {
            $data['links'] = $result->getLinks();
        }

        $payload = $this->serializer->serialize($data, 'json');
        $status = $this->getStatusCode($request);

        $response = new JsonResponse($payload, $status, [], true);
        $event->setResponse($response);
    }

    private function getStatusCode(Request $request): int
    {
        return match ($request->getMethod()) {
//            'POST' => Response::HTTP_CREATED, // need return 200
            'DELETE' => Response::HTTP_NO_CONTENT,
            default => Response::HTTP_OK,
        };
    }
}