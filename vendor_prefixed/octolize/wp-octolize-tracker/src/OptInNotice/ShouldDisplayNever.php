<?php

namespace OctolizeShippingAustraliaPostVendor\Octolize\Tracker\OptInNotice;

/**
 * Should display never.
 */
class ShouldDisplayNever implements ShouldDisplay
{
    /**
     * @inheritDoc
     */
    public function should_display()
    {
        return \false;
    }
}
