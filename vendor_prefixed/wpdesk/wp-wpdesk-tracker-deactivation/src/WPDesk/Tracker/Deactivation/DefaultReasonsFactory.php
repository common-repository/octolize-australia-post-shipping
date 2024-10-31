<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Tracker\Deactivation;

class DefaultReasonsFactory implements ReasonsFactory
{
    public function createReasons(): array
    {
        $reasons = [];
        $reason = new Reason('not_selected', '', '', \false, '', \true, \true);
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('plugin_stopped_working', __('The plugin suddenly stopped working', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('broke_my_site', __('The plugin broke my site', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('found_better_plugin', __('I have found a better plugin', 'octolize-australia-post-shipping'), '', \true, __('What\'s the plugin\'s name?', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('plugin_for_short_period', __('I only needed the plugin for a short period', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('no_longer_need', __('I no longer need the plugin', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('temporary_deactivation', __('It\'s a temporary deactivation. I\'m just debugging an issue.', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new Reason('other', __('Other', 'octolize-australia-post-shipping'), '', \true, __('Please let us know how we can improve our plugin', 'octolize-australia-post-shipping'));
        $reasons[$reason->getValue()] = $reason;
        return $reasons;
    }
}
