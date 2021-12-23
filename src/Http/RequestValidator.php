<?php


namespace OSN\Framework\Http;


use OSN\Framework\Exceptions\PropertyNotFoundException;

trait RequestValidator
{
    protected array $errors = [];
    protected bool $fixFieldNames = true;

    protected function addError($field, $rule, $errmsg)
    {
        $this->errors[$field][$rule] = $errmsg;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return empty($this->errors);
    }

    public function hasField(string $field): bool
    {
        try {
            $value = $this->{$field};
            return true;
        }
        catch (PropertyNotFoundException $e) {
            return false;
        }
    }

    public function validate(array $customRules = null): bool
    {
        session()->unset('__validation_errors');

        $rules = $customRules ?? $this->rules();

        foreach ($rules as $field => $ruleList) {
            try {
                $value = $this->{$field};
            }
            catch (PropertyNotFoundException $e) {
                $notSet = true;
            }

            $readableField = trim(str_replace('_', ' ', $field));

            foreach ($ruleList as $rule) {
                if ($rule === "int" && isset($this->{$field}) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $rule, "The $readableField must be an integer");
                }

                if ($rule === "float" && isset($this->{$field}) && !filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $this->addError($field, $rule, "The $readableField must be a float");
                }

                if ($rule === "number" && isset($this->{$field}) && !($value === 0 || filter_var($value, FILTER_VALIDATE_FLOAT) || filter_var($value, FILTER_VALIDATE_INT))) {
                    $this->addError($field, $rule, "The $readableField must be a valid number");
                }

                if (preg_match("/max:\d+/", $rule)) {
                    $pos = strpos($rule, ":") + 1;
                    $maxValue = substr($rule, $pos);

                    if (strlen($value) > $maxValue) {
                        $this->addError($field, "max", "The maximum length of $readableField must be $maxValue");
                    }
                }

                if (preg_match("/min:\d+/", $rule)) {
                    $pos = strpos($rule, ":") + 1;
                    $minValue = substr($rule, $pos);

                    if (strlen($value) < $minValue) {
                        $this->addError($field, "min", "The minimum length of $readableField must be $minValue");
                    }
                }

                if ($rule === "email" && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $rule, "The $readableField must be a valid email");
                }

                if ($rule === "required" && (isset($notSet) || trim($value) == '' || !isset($value))) {
                    $this->addError($field, $rule, "The $readableField is required");
                }

                if ($rule === "confirmed") {
                    $newField = $field . "_confirmation";

                    if (!$this->hasField($newField) || $value !== $this->$newField) {
                        $this->addError($newField, $rule, "The $readableField confirmation must be same as $readableField");
                    }
                }
            }
        }

        if (!empty($this->getErrors())) {
            return false;
        }

        return true;
    }
}
