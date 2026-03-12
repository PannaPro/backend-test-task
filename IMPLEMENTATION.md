# Implementation Notes


## Как запустить

- git clone ...

- В директрии проекта make init

- Тесты make test

## Что реализовано
- `POST /calculate-price` реализован в [src/Controller/CalculatePriceController.php]
- `POST /purchase` реализован в [src/Controller/PurchaseController.php]
- Валидация входных DTO сделана через Symfony Validator:
  - [src/Model/CalculatePriceRequestDto.php]
  - [src/Model/PurchaseRequestDto.php]
- Валидация налогового номера вынесена в кастомный валидатор:
  - [src/Validator/ValidTaxNumber.php]
  - [src/Validator/ValidTaxNumberValidator.php]
- Общий сценарий расчета цены заказа вынесен в [src/Service/ProductPricing/ProductPricingService.php]
- Поддержка разных платежных процессоров реализована через gateway-слой:
  - [src/Service/Payment/PaymentGatewayInterface.php]
  - [src/Service/Payment/PaymentGatewayResolver.php]
  - [src/Service/Payment/PaypalPaymentGateway.php]
  - [src/Service/Payment/StripePaymentGateway.php]

## Что оставлено заглушкой или упрощено
- Источник правил по налогам сейчас захардкожен в памяти в [src/Service/TaxRuleProvider.php]
  Это заглушка под будущий Redis/кеш. 
- Для бесплатного заказа (`finalPriceInCents === 0`) в purchase-сценарии оплаты не вызывается.
  Отдельный статус бесплатного заказа не вводился. Все будет зависить от будущих бизнес правил, если заказ вышел на сумму, которую заведомо не принимает платежный шлюз. 
- Возврат результата покупки упрощен до `{"status":"ok"}` без отдельной модели заказа/платежа. 
- Логика работы с платежными SDK ограничена только адаптацией их контрактов под единый интерфейс, без хранения transaction id и без персистентной истории платежей.

## Технические решения
- Деньги внутри доменной логики считаются в минимальных единицах (`int`, cents).
- Преобразование суммы под формат конкретного SDK делается только на уровне payment gateway.
- Общая логика расчета вынесена в `ProductPricingService`.

## Тесты
- Unit tests покрывают:
  - расчет цены
  - pricing service
  - purchase service
  - payment gateways
- Есть 2 функциональных теста на успешный и неуспешный сценарии.
