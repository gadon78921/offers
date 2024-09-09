<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime;

use App\Esb\In\RetailRuleCustomerOrderReadyTime\Infrastructure\RetailDeliveryTimeFromSuppliersAccessObject;
use App\Esb\In\RetailRuleCustomerOrderReadyTime\Infrastructure\RetailDeliveryTimeFromSuppliersTransferObject;
use App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages\RetailRuleCustomerOrderReadyTimeMessage;
use App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages\RuleDataCustomerOrderReadyTime;

/**
 * @codeCoverageIgnore
 */
final class RetailRuleCustomerOrderReadyTimeHandler
{
    public function __construct(
        private readonly RetailDeliveryTimeFromSuppliersAccessObject $dao
    ) {}

    public function handle(RetailRuleCustomerOrderReadyTimeMessage $message): void
    {
        $this->dao->remove($message->headId);

        [$rulesToIgnore, $rulesToSave] = $message->actualRules->rules->partition(static function ($key, RuleDataCustomerOrderReadyTime $rule) use ($message) {
            return $message->deleted || $rule->deleted;
        });

        $rulesToSave->map(function (RuleDataCustomerOrderReadyTime $rule) use ($message) {
            $this->dao->save(new RetailDeliveryTimeFromSuppliersTransferObject(
                $message->headId,
                $rule->ruleId,
                $message->regionId,
                $message->supplierId,
                $message->firmId,
                $message->daysToSendOrders,
                $message->orderSendTime,
                $rule->hoursUntilReady,
                $rule->dateFrom,
            ));
        });
    }
}
