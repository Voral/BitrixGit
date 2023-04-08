<?php

namespace Vasoft\Git\Infrastructure\Controllers\ActionFilters;

use Bitrix\Main\Engine;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class RightsFilter extends Engine\ActionFilter\Base
{
    /**
     * @param Event $event
     * @return EventResult|null
     */
    public function onBeforeAction(Event $event)
    {
        if (\CMain::GetGroupRight("vasoft.git") === 'D') {
            return new EventResult(EventResult::ERROR, null, null, $this);
        }
        return null;
    }
}