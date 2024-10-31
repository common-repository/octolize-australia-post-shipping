<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\ShowDecision;

class ConstantNotDefinedStrategy implements ShouldShowStrategy
{
    /**
     * @var string
     */
    private string $constant;
    public function __construct(string $constant)
    {
        $this->constant = $constant;
    }
    public function shouldDisplay(): bool
    {
        return !defined($this->constant);
    }
}
