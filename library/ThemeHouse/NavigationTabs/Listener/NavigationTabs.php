<?php

class ThemeHouse_NavigationTabs_Listener_NavigationTabs
{

    public static function navigationTabs(array &$extraTabs, $selectedTabId)
    {
        $xenOptions = XenForo_Application::get('options');

        $listeners = XenForo_CodeEvent::getEventListeners('navigation_tabs');

        foreach ($listeners['_'] as $key => $callback) {
            if ($callback[0] == __CLASS__) {
                $listeners['_'] = array_slice($listeners['_'], $key+1);
            }
        }

        if (!$listeners) {
            return true;
        }

        foreach ($listeners as $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_callable($callback)) {
                    $return = call_user_func_array($callback, array(&$extraTabs, $selectedTabId));
                    foreach ($extraTabs as $key => $extraTab) {
                        if (!isset($extraTab['priority']) && isset($xenOptions->th_navigationTabs_priority[$callback[0] . '::' . $callback[1]])) {
                            $extraTabs[$key]['priority'] = $xenOptions->th_navigationTabs_priority[$callback[0] . '::' . $callback[1]];
                        }
                    }
                    if ($return === false) {
                        return false;
                    }
                }
            }
        }

        foreach ($extraTabs as $tabKey => $extraTab) {
            if (isset($extraTab['nodeTab']['nat_display_order'])) {
                $extraTabs[$tabKey]['priority'] = $extraTab['nodeTab']['nat_display_order'];
            } elseif (!isset($extraTab['priority'])) {
                $extraTabs[$tabKey]['priority'] = 10;
            }
        }

        uasort($extraTabs, array('self', 'comparePriority'));

        return false;
    }

    public static function comparePriority($a, $b)
    {
        return $a['priority'] - $b['priority'];
    }
}