<?php
if (!function_exists('enum_exists')) {
    /**
     * Check if a value exists in an enum class.
     *
     * @param string $enumClass The enum class name.
     * @param mixed $value The value to check.
     * @return bool
     */
    function enum_exists(string $enumClass, $value): bool
    {
        return in_array($value, array_column($enumClass::cases(), 'value'));
    }
}
