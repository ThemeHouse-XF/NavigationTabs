<?php

class ThemeHouse_NavigationTabs_Option_Priority
{

    public static function renderSpinBoxes(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        /* @var $codeEventModel XenForo_Model_CodeEvent */
        $codeEventModel = XenForo_Model::create('XenForo_Model_CodeEvent');

        $codeEventListeners = $codeEventModel->getAllEventListeners();

        /* @var $addOnModel XenForo_Model_AddOn */
        $addOnModel = XenForo_Model::create('XenForo_Model_AddOn');

        $addOns = $addOnModel->getAllAddOns();

        foreach ($codeEventListeners as $codeEventListener) {
            if ($codeEventListener['event_id'] == 'navigation_tabs') {
                $callback = $codeEventListener['callback_class'] . '::' . $codeEventListener['callback_method'];
                $addOns[$codeEventListener['addon_id']]['callbacks'][] = $callback;
                if (!isset($preparedOption['option_value'][$callback])) {
                    $preparedOption['option_value'][$callback] = 10;
                }
            }
        }

        unset($addOns['NodesAsTabs']);
        unset($addOns['ThemeHouse_NavigationTabs']);
        foreach ($addOns as $addOnId => $addOn) {
            if (empty($addOn['callbacks'])) {
                unset($addOns[$addOnId]);
            }
        }

        $preparedOption['formatParams']['addOns'] = $addOns;

        return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
            'th_option_list_priorities_navigationtabs', $view, $fieldPrefix, $preparedOption, $canEdit
        );
    }
}