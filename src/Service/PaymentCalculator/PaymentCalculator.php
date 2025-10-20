<?php

namespace App\Service\PaymentCalculator;

use App\Exceptions\InvalidPaymentException;

class PaymentCalculator
{
    private const VALID_METHODS = ['paypal', 'payonline'];

    private const CALCULATORS = [
        'paypal' => PayPalCalculator::class,
        'payonline' => PayOnlineCalculator::class,
    ];

    public static function create(string $paymentMethod): PaymentCalculatorInterface
    {
        if (!self::isValidMethod($paymentMethod)) {
            throw new InvalidPaymentException($paymentMethod, self::VALID_METHODS);
        }

        $calculatorClass = self::CALCULATORS[$paymentMethod];
        return new $calculatorClass();
    }

    public static function getValidMethods(): array
    {
        return self::VALID_METHODS;
    }

    private static function isValidMethod(string $method): bool
    {
        return isset(self::CALCULATORS[$method]);
    }
}
