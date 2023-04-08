<?php
/** @bxnolanginspection */

namespace Vasoft\Git\Infrastructure\Settings\Fields;

use Vasoft\Git\Infrastructure\Settings\ModuleConfig;
use Vasoft\Git\Infrastructure\Settings\Properties;

abstract class Field
{
    protected ModuleConfig $config;
    protected array $fields = [];
    protected string $code;
    protected string $description;
    protected string $note = '';
    /**
     * @var callable
     */
    protected $getter;

    public function __construct(string $code, callable $getter)
    {

        $this->code = $code;
        $this->description = Properties::description($code);

        $this->getter = $getter;
    }

    public function render(): string
    {
        $input = $this->renderInput();
        $result = <<<HTML
            <tr>
                <td style="width:50%;vertical-align:top">$this->description</td>
                <td style="width:50%;vertical-align:top">$input</td>
            </tr>
HTML;
        if ($this->note !== '') {
            $result .= <<<HTML
            <tr>
                <td style="width:50%;vertical-align:top"></td>
                <td style="vertical-align:top">$this->note</td>
            </tr>
HTML;
        }
        return $result;
    }

    abstract protected function renderInput(): string;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /** @noinspection PhpUnused */
    public function configureNote(string $text): self
    {
        $this->note = $text;
        return $this;
    }
}