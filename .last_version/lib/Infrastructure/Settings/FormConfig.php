<?php
/** @noinspection DuplicatedCode */

namespace Vasoft\Git\Infrastructure\Settings;

use Bitrix\Main\Localization\Loc;
use Vasoft\Git\Infrastructure\Settings\Fields\Field;
use Vasoft\Git\Infrastructure\Settings\Fields\TextField;

final class FormConfig
{
    public const TAB_OPTIONS = 'options';
    private ModuleConfig $config;
    private array $fields = [];
    /**
     * @var Field[]
     */
    private array $fieldIndex = [];

    public function __construct(ModuleConfig $config)
    {
        $this->config = $config;
        $this->configure();
    }

    private function configure(): void
    {
        $this->configureOptionsTab();
    }

    private function configureOptionsTab(): void
    {
        $this
            ->configureField(
                self::TAB_OPTIONS,
                new TextField(Properties::HOME, [$this->config, 'getHome'])
            );
    }

    /** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection */
    private function configureField(string $tabCode, Field $field): self
    {
        $this->fieldIndex[$field->getCode()] = $field;
        if (array_key_exists($tabCode, $this->fields)) {
            $this->fields[$tabCode][$field->getCode()] = &$this->fieldIndex[$field->getCode()];
        } else {
            $this->fields[$tabCode] = [$field->getCode() => &$this->fieldIndex[$field->getCode()]];
        }
        return $this;
    }

    public function getTabs(): array
    {
        return [
            ["DIV" => self::TAB_OPTIONS, "TAB" => Loc::getMessage('VASOFT_GIT_TAB_OPTIONS'), "TITLE" => Loc::getMessage('VASOFT_GIT_TAB_OPTIONS')],
        ];
    }

    /**
     * @param string $tabCode
     * @return Field[]
     */
    public function getFields(string $tabCode): array
    {
        return array_key_exists($tabCode, $this->fields) ? $this->fields[$tabCode] : [];
    }
}
