<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleDeliveryTime;

use App\Esb\In\RetailRuleDeliveryTime\Infrastructure\RetailRuleDeliveryTimeAccessObject;
use App\Esb\In\RetailRuleDeliveryTime\Infrastructure\RetailRuleDeliveryTimeTransferObject;
use App\Esb\In\RetailRuleDeliveryTime\Messages\RuleDataDeliveryTime;
use App\Esb\In\RetailRuleDeliveryTime\Messages\RuleDeliveryTimeMessage;
use App\Esb\In\RetailTradePoint\RetailTradePointRepository;
use App\OffersBuilding\Commands\BuildOffersByTradePointIds;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailRuleDeliveryTimeHandler
{
    public function __construct(
        private readonly RetailRuleDeliveryTimeAccessObject $dao,
        private readonly RetailTradePointRepository $tradePointRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function handle(RuleDeliveryTimeMessage $message): void
    {
        $this->dao->remove($message->headId);

        [$rulesToIgnore, $rulesToSave] = $message->actualRules->rules->partition(static function ($key, RuleDataDeliveryTime $rule) use ($message) {
            return $message->deleted || $rule->deleted;
        });

        $rulesToSave->map(function (RuleDataDeliveryTime $rule) use ($message) {
            $this->dao->save(new RetailRuleDeliveryTimeTransferObject(
                $message->headId,
                $rule->ruleId,
                $message->regionId,
                $message->supplierId,
                $message->firmId,
                $rule->isForTZOnly,
                $rule->dateFrom,
            ));
        });

        if (null !== $message->firmId) {
            $tradePointIds = $this->tradePointRepository->getTradePointIdsByFirmId((string) $message->firmId);
            $this->messageBus->dispatch(new BuildOffersByTradePointIds($tradePointIds));

            return;
        }

        $tradePointIdsByKladrId = $this->tradePointRepository->getTradePointIdsBySupplierId($message->supplierId);
        foreach ($tradePointIdsByKladrId as $tradePointIds) {
            $this->messageBus->dispatch(new BuildOffersByTradePointIds($tradePointIds));
        }
    }
}
