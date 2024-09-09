<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams;

use App\Esb\In\RetailRuleTradePointParams\Infrastructure\RetailRuleDataTradePointParamsAccessObject;
use App\Esb\In\RetailRuleTradePointParams\Infrastructure\RetailRuleDataTradePointParamsTransferObject;
use App\Esb\In\RetailRuleTradePointParams\Messages\RuleDataTradePointParams;
use App\Esb\In\RetailRuleTradePointParams\Messages\RuleTradePointParamsMessage;

/**
 * @codeCoverageIgnore
 */
final class RetailRuleTradePointParamsMessageHandler
{
    public function __construct(
        private readonly RetailRuleDataTradePointParamsAccessObject $dao,
    ) {}

    public function handle(RuleTradePointParamsMessage $message): void
    {
        $this->dao->remove($message->headId);

        [$rulesToIgnore, $rulesToSave] = $message->actualRules->rules->partition(static function ($key, RuleDataTradePointParams $rule) use ($message) {
            return $message->deleted || $rule->deleted;
        });

        $rulesToSave->map(function (RuleDataTradePointParams $rule) use ($message) {
            if (null !== $message->tradePointId) {
                $this->dao->save(new RetailRuleDataTradePointParamsTransferObject(
                    $message->headId,
                    $rule->ruleId,
                    $message->tradePointId,
                    $rule->workStartHour,
                    $rule->workEndHour,
                    $rule->daysOfWork,
                    $rule->dateFrom,
                ));
            }
        });
    }
}
