<?php

/**
 * @file classes/SynchronizeCitations.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SynchronizeCitations
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to synchronize the number of citations
 */

namespace APP\plugins\generic\mostCited\classes;

use PKP\form\Form;

class Settings
{
    /**
     * Constructor
     */
    public function __construct(
        public ?string $provider = null,
        public ?string $scopusKey = null,
        public ?string $crossrefUser = null,
        public ?string $crossrefRole = null,
        public ?string $crossrefPassword = null,
        public ?int $quantity = null,
        public ?int $position = null,
        public ?array $header = null
    ) {
    }

    /**
     * Retrieves settings as a JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Retrieves settings as an array
     */
    public function toArray(): array
    {
        $settings = [];
        foreach ($this as $name => $value) {
            $settings[$name] = $value;
        }
        return $settings;
    }

    /**
     * Reads settings from a JSON string
     */
    public function fromJson(?string $settings): static
    {
        $this->fromArray(json_decode($settings, true) ?: []);
        return $this;
    }

    /**
     * Reads settings from an array
     */
    public function fromArray(array $settings): static
    {
        foreach ($this as $name => $value) {
            $this->$name = $settings[$name] ?? null;
        }
        return $this;
    }

    /**
     * Writes settings to a Form
     */
    public function toForm(Form $form): static
    {
        foreach ($this as $name => $value) {
            $form->setData($name, $value);
        }
        return $this;
    }

    /**
     * Reads settings from a Form instance
     */
    public function fromForm(Form $form): static
    {
        foreach ($this as $name => $value) {
            $this->$name = $form->getData($name);
        }
        return $this;
    }

    /**
     * Creates a new instance
     */
    public static function create(): static
    {
        return new static();
    }
}
