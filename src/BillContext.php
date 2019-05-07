<?php
declare(strict_types=1);
namespace BillingBoss;

/**
 * Class BillContext
 *
 * @category BillingBoss
 * @package  BillingBoss
 * @author   Ransford Okpoti <ranskills@yahoo.co.uk>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/ranskills/billing-boss-php
 * @since    1.0.0
 */
final class BillContext
{
    private $amount;
    private $structure;

    /**
     * Constructor
     *
     * @param float  $amount    The amount to be billed
     * @param string $structure The billing structure to be applied on $amount
     */
    public function __construct(float $amount, string $structure)
    {
        $this->amount = $amount;
        $this->setStructure($structure);
    }

    /**
     * Returns the amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Sets the amount and returns this instance
     *
     * @param float $amount The amount to be billed
     *
     * @return BillContext
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Returns the structure for this context
     *
     * @return string
     */
    public function getStructure(): string
    {
        return $this->structure;
    }

    /**
     * Sets the billing structure for the context
     *
     * @param string $structure The billing structure
     *
     * @return BillContext
     */
    public function setStructure(string $structure)
    {
        $this->structure = trim(preg_replace('/\s+/', ' ', $structure));
        return $this;
    }
}
