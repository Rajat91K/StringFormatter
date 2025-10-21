<?php

class StringFormatter {
    private $parts          = [];
    private $delimiter      = ', ';
    private $htmlMode       = false;
    private $sections       = [];
    private $currentSection = 0;
    private $autoClean      = true;
    private $styles         = [];

    public static function create() {
        return new self();
    }

    /**
     * Enable HTML mode for web output
     */
    public function htmlMode($enabled = true) {
        $this->htmlMode = $enabled;

        return $this;
    }

    /**
     * Enable/disable auto-cleaning of delimiters at line edges
     */
    public function autoClean($enabled = true) {
        $this->autoClean = $enabled;

        return $this;
    }

    /**
     * Add custom CSS class to next element
     */
    public function withClass($className) {
        $this->styles['class'] = $className;

        return $this;
    }

    /**
     * Add inline CSS style to next element
     */
    public function withStyle($style) {
        $this->styles['style'] = $style;

        return $this;
    }

    /**
     * Add both class and style to next element
     */
    public function withCss($class = null, $style = null) {
        if ($class !== null) {
            $this->styles['class'] = $class;
        }

        if ($style !== null) {
            $this->styles['style'] = $style;
        }

        return $this;
    }

    /**
     * Add a simple value with optional prefix
     */
    public function add($value, $prefix = '', $separator = ': ') {
        if ($this->hasValue($value)) {
            $value         = $this->normalizeValue($value);
            $formatted     = $prefix ? $prefix.$separator.$value : $value;
            $this->parts[] = ['content' => $formatted, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add value with both prefix and suffix
     */
    public function addWithSuffix($value, $prefix = '', $suffix = '', $separator = ': ') {
        if ($this->hasValue($value)) {
            $value         = $this->normalizeValue($value);
            $formatted     = $prefix ? $prefix.$separator.$value.$suffix : $value.$suffix;
            $this->parts[] = ['content' => $formatted, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add value only (no prefix/suffix)
     */
    public function addRaw($value) {
        if ($this->hasValue($value)) {
            $this->parts[] = ['content' => $this->normalizeValue($value), 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add a line break (br tag in HTML mode, \n in text mode)
     */
    public function addBreak() {
        $break         = $this->htmlMode ? '<br>' : "\n";
        $this->parts[] = ['content' => $break, 'type' => 'break'];

        return $this;
    }

    public function addBreakIf($condition) {
        if ($condition) {
            $this->addBreak();
        }

        return $this;
    }

    /**
     * Add value and then a line break
     */
    public function addLine($value, $prefix = '', $separator = ': ') {
        if ($this->hasValue($value)) {
            return $this->add($value, $prefix, $separator)->addBreak();
        }

        return $this;
    }

    /**
     * Add raw value and then a line break
     */
    public function addRawLine($value) {
        if ($this->hasValue($value)) {
            return $this->addRaw($value)->addBreak();
        }

        return $this;
    }

    /**
     * Add multiple values on SAME line with delimiter between them
     * Automatically skips empty values
     *
     * @param array       $data            Data array
     * @param array       $keys            Keys to extract OR associative array ['key' => 'Prefix']
     * @param string|null $inlineDelimiter Delimiter between values (default: class delimiter)
     * @param string      $separator       Separator between prefix and value (default: ': ')
     */
    public function addInline($data, $keys, $inlineDelimiter = null, $separator = ': ') {
        $values = [];

        foreach ($keys as $key => $prefixOrKey) {
            $actualKey = null;
            $prefix    = '';

            // Check if keys array is associative (with prefixes)
            if (is_string($key)) {
                // Format: ['mobile_no' => 'M', 'pan_no' => 'PAN']
                $actualKey = $key;
                $prefix    = $prefixOrKey;
            } elseif (is_array($prefixOrKey)) {
                // Format: [['key' => 'mobile_no', 'prefix' => 'M']]
                $actualKey = $prefixOrKey['key']    ?? $prefixOrKey[0] ?? null;
                $prefix    = $prefixOrKey['prefix'] ?? $prefixOrKey[1] ?? '';
            } else {
                // Format: ['mobile_no', 'pan_no']
                $actualKey = $prefixOrKey;
                $prefix    = '';
            }

            $value = null;

            // Handle array data with keys
            if (is_array($data) && isset($data[$actualKey])) {
                $value = $data[$actualKey];
            }
            // Handle direct values (when keys are actually values)
            elseif (!is_array($data)) {
                $value = $actualKey;
            }

            // Only add if value exists and is not empty
            if ($this->hasValue($value)) {
                $normalized = $this->normalizeValue($value);

                if ($prefix) {
                    $values[] = $prefix.$separator.$normalized;
                } else {
                    $values[] = $normalized;
                }
            }
        }

        if (!empty($values)) {
            $delimiter     = $inlineDelimiter !== null ? $inlineDelimiter : $this->delimiter;
            $this->parts[] = ['content' => implode($delimiter, $values), 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add multiple values on SAME line and then break
     * Automatically skips empty values
     *
     * @param array       $data            Data array
     * @param array       $keys            Keys to extract OR associative array ['key' => 'Prefix']
     * @param string|null $inlineDelimiter Delimiter between values (default: class delimiter)
     * @param string      $separator       Separator between prefix and value (default: ': ')
     */
    public function addInlineLine($data, $keys, $inlineDelimiter = null, $separator = ': ') {
        $initialCount = count($this->parts);
        $this->addInline($data, $keys, $inlineDelimiter, $separator);

        // Only add break if something was actually added
        if (count($this->parts) > $initialCount) {
            $this->addBreak();
        }

        return $this;
    }

    /**
     * Add multiple values, each on a NEW line
     * Automatically skips empty values
     */
    public function addLines($values, $prefix = '', $separator = ': ') {
        foreach ($values as $value) {
            if ($this->hasValue($value)) {
                $this->addLine($value, $prefix, $separator);
            }
        }

        return $this;
    }

    /**
     * Add multiple values from array, each on a NEW line
     * Automatically skips empty/missing keys
     */
    public function addMultiple($data, $keys, $prefix = '', $separator = ': ') {
        foreach ($keys as $key) {
            if (isset($data[$key]) && $this->hasValue($data[$key])) {
                $this->add($data[$key], $prefix, $separator)->addBreak();
            }
        }

        return $this;
    }

    /**
     * Add conditional value (only if condition is true)
     */
    public function addIf($condition, $value, $prefix = '', $separator = ': ') {
        if ($condition && $this->hasValue($value)) {
            $value         = $this->normalizeValue($value);
            $formatted     = $prefix ? $prefix.$separator.$value : $value;
            $this->parts[] = ['content' => $formatted, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add section with title (bold in HTML mode)
     */
    public function addSection($title, $content = null, $class = null, $style = null) {
        if (!$this->hasValue($title)) {
            return $this;
        }

        $title = $this->normalizeValue($title);

        if ($this->htmlMode) {
            $attributes    = $this->buildHtmlAttributes($class, $style);
            $this->parts[] = ['content' => "<strong{$attributes}>{$title}</strong>", 'type' => 'section'];
        } else {
            $this->parts[] = ['content' => strtoupper($title), 'type' => 'section'];
        }

        $this->addBreak();

        if ($content !== null && $this->hasValue($content)) {
            $this->addRaw($content);
        }

        return $this;
    }

    /**
     * Add bold/strong text
     */
    public function addBold($value, $class = null, $style = null) {
        if ($this->hasValue($value)) {
            $value = $this->normalizeValue($value);

            if ($this->htmlMode) {
                $attributes    = $this->buildHtmlAttributes($class, $style);
                $this->parts[] = ['content' => "<strong{$attributes}>{$value}</strong>", 'type' => 'inline'];
            } else {
                $this->parts[] = ['content' => strtoupper($value), 'type' => 'inline'];
            }
        }

        return $this;
    }

    /**
     * Add bold text and then a break
     */
    public function addBoldLine($value, $class = null, $style = null) {
        if ($this->hasValue($value)) {
            return $this->addBold($value, $class, $style)->addBreak();
        }

        return $this;
    }

    /**
     * Wrap content in a div with optional class and style
     */
    public function addDiv($content, $class = null, $style = null) {
        if (!$this->hasValue($content)) {
            return $this;
        }

        $content    = $this->normalizeValue($content);
        $attributes = $this->buildHtmlAttributes($class, $style);

        if ($this->htmlMode) {
            $this->parts[] = ['content' => "<div{$attributes}>{$content}</div>", 'type' => 'block'];
        } else {
            $this->parts[] = ['content' => $content, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Wrap content in a span with optional class and style
     */
    public function addSpan($content, $class = null, $style = null) {
        if (!$this->hasValue($content)) {
            return $this;
        }

        $content    = $this->normalizeValue($content);
        $attributes = $this->buildHtmlAttributes($class, $style);

        if ($this->htmlMode) {
            $this->parts[] = ['content' => "<span{$attributes}>{$content}</span>", 'type' => 'inline'];
        } else {
            $this->parts[] = ['content' => $content, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Add styled line (div with content)
     */
    public function addStyledLine($content, $class = null, $style = null) {
        if (!$this->hasValue($content)) {
            return $this;
        }

        $content = $this->normalizeValue($content);

        if ($this->htmlMode) {
            $attributes    = $this->buildHtmlAttributes($class, $style);
            $this->parts[] = ['content' => "<div{$attributes}>{$content}</div>", 'type' => 'block'];
        } else {
            $this->parts[] = ['content' => $content, 'type' => 'inline'];
            $this->addBreak();
        }

        return $this;
    }

    /**
     * Add inline styled content (span)
     */
    public function addStyled($content, $class = null, $style = null) {
        return $this->addSpan($content, $class, $style);
    }

    /**
     * Add content with custom tag
     */
    public function addTag($tag, $content, $class = null, $style = null) {
        if (!$this->hasValue($content)) {
            return $this;
        }

        $content = $this->normalizeValue($content);

        if ($this->htmlMode) {
            $attributes    = $this->buildHtmlAttributes($class, $style);
            $this->parts[] = ['content' => "<{$tag}{$attributes}>{$content}</{$tag}>", 'type' => 'inline'];
        } else {
            $this->parts[] = ['content' => $content, 'type' => 'inline'];
        }

        return $this;
    }

    /**
     * Start a styled section/container
     */
    public function startSection($class = null, $style = null) {
        if ($this->htmlMode) {
            $attributes    = $this->buildHtmlAttributes($class, $style);
            $this->parts[] = ['content' => "<div{$attributes}>", 'type' => 'container-open'];
        }

        return $this;
    }

    /**
     * End a styled section/container
     */
    public function endSection() {
        if ($this->htmlMode) {
            $this->parts[] = ['content' => "</div>", 'type' => 'container-close'];
        }

        return $this;
    }

    /**
     * Add value from array with key, with prefix
     * Automatically checks if key exists and has value
     */
    public function addFromArray($data, $key, $prefix = '', $separator = ': ') {
        if (is_array($data) && isset($data[$key]) && $this->hasValue($data[$key])) {
            return $this->add($data[$key], $prefix, $separator);
        }

        return $this;
    }

    /**
     * Add value from array with key and line break
     * Automatically checks if key exists and has value
     */
    public function addLineFromArray($data, $key, $prefix = '', $separator = ': ') {
        if (is_array($data) && isset($data[$key]) && $this->hasValue($data[$key])) {
            return $this->addLine($data[$key], $prefix, $separator);
        }

        return $this;
    }

    /**
     * Set custom delimiter (used between inline items)
     */
    public function withDelimiter($delimiter) {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set delimiter for line breaks
     */
    public function withLineDelimiter() {
        $this->delimiter = $this->htmlMode ? '<br>' : "\n";

        return $this;
    }

    /**
     * Get count of parts added
     */
    public function count() {
        return count($this->parts);
    }

    /**
     * Check if formatter has any parts
     */
    public function isEmpty() {
        return empty($this->parts);
    }

    /**
     * Clear all parts
     */
    public function clear() {
        $this->parts = [];
        $this->resetSections();

        return $this;
    }

    /**
     * Convert to string with smart delimiter cleaning
     */
    public function toString($delimiter = null) {
        if (empty($this->parts)) {
            return '';
        }

        // If sections exist (split was used), combine them
        if (!empty($this->sections) || $this->currentSection > 0) {
            return $this->buildCombined($delimiter ?: ($this->htmlMode ? '<br>' : "\n"));
        }

        // First pass: organize parts into output structure
        $output      = [];
        $currentLine = [];

        foreach ($this->parts as $part) {
            if ($part['type'] === 'container-open' || $part['type'] === 'container-close') {
                // Close current line before container tags
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
                $output[] = ['type' => 'container', 'content' => $part['content']];
            } elseif ($part['type'] === 'break') {
                // End current line
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
            } elseif ($part['type'] === 'block') {
                // Block elements go on their own
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
                $output[] = ['type' => 'line', 'content' => $part['content']];
            } else {
                $currentLine[] = $part['content'];
            }
        }

        // Add remaining line
        if (!empty($currentLine)) {
            $output[] = ['type' => 'line', 'content' => implode('', $currentLine)];
        }

        // Second pass: build final string
        $result       = '';
        $contentLines = [];

        foreach ($output as $item) {
            if ($item['type'] === 'container') {
                // Output accumulated content lines first
                if (!empty($contentLines)) {
                    $lineBreak = $this->htmlMode ? '<br>' : "\n";
                    $result .= implode($lineBreak, $contentLines);
                    $contentLines = [];
                }
                // Add the container tag
                $result .= $item['content'];
            } else {
                // It's a content line
                $content = $item['content'];

                // Clean the line if needed
                if ($this->autoClean && !(strpos($content, '<div') === 0 || strpos($content, '</div>') === 0)) {
                    $content = $this->cleanLine($content);
                }

                // Only add non-empty lines
                if (trim($content) !== '') {
                    $contentLines[] = $content;
                }
            }
        }

        // Add any remaining content lines
        if (!empty($contentLines)) {
            $lineBreak = $this->htmlMode ? '<br>' : "\n";
            $result .= implode($lineBreak, $contentLines);
        }

        return $result;
    }

    /**
     * Magic method for string conversion
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Clean line from leading/trailing delimiters and whitespace
     */
    private function cleanLine($line) {
        // Trim whitespace
        $line = trim($line);

        // Remove leading/trailing delimiters
        $delimiters = [', ', ',', ': ', ':', '; ', ';', ' - ', '-', ' | ', '|'];

        do {
            $changed = false;

            foreach ($delimiters as $delim) {
                $trimmed = trim($line);

                // Remove from start
                if (substr($trimmed, 0, strlen($delim)) === $delim) {
                    $line    = substr($trimmed, strlen($delim));
                    $changed = true;
                }

                $trimmed = trim($line);

                // Remove from end
                if (substr($trimmed, -strlen($delim)) === $delim) {
                    $line    = substr($trimmed, 0, -strlen($delim));
                    $changed = true;
                }
            }
        } while ($changed);

        return trim($line);
    }

    /**
     * Check if value is not empty and worth adding
     */
    private function hasValue($value) {
        // Null or empty string
        if ($value === null || $value === '') {
            return false;
        }

        // Keep numeric zero
        if ($value === 0 || $value === '0') {
            return true;
        }

        // Empty array
        if (is_array($value)) {
            return !empty($value);
        }

        // String with only whitespace
        if (is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }

    /**
     * Normalize value to prevent array-to-string conversion errors
     */
    private function normalizeValue($value) {
        if (is_array($value)) {
            $filtered = array_filter($value, function($v) {
                return $this->hasValue($v);
            });

            return implode(', ', $filtered);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return (string)$value;
    }

    /**
     * Build HTML attributes string from class and style
     */
    private function buildHtmlAttributes($class = null, $style = null) {
        $attributes = [];

        // Use provided class or stored class
        $finalClass = $class ?? ($this->styles['class'] ?? null);

        if ($finalClass) {
            $attributes[] = 'class="'.htmlspecialchars($finalClass, ENT_QUOTES, 'UTF-8').'"';
        }

        // Use provided style or stored style
        $finalStyle = $style ?? ($this->styles['style'] ?? null);

        if ($finalStyle) {
            $attributes[] = 'style="'.htmlspecialchars($finalStyle, ENT_QUOTES, 'UTF-8').'"';
        }

        // Clear stored styles after use
        $this->styles = [];

        return !empty($attributes) ? ' '.implode(' ', $attributes) : '';
    }

    /**
     * Mark current parts as complete section and start new section
     */
    public function split() {
        if (!empty($this->parts)) {
            $this->sections[$this->currentSection] = $this->buildCurrentParts();
            $this->currentSection++;
            $this->parts = [];
        }

        return $this;
    }

    /**
     * Build current parts into a string
     */
    private function buildCurrentParts() {
        // First pass: organize parts
        $output      = [];
        $currentLine = [];

        foreach ($this->parts as $part) {
            if ($part['type'] === 'container-open' || $part['type'] === 'container-close') {
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
                $output[] = ['type' => 'container', 'content' => $part['content']];
            } elseif ($part['type'] === 'break') {
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
            } elseif ($part['type'] === 'block') {
                if (!empty($currentLine)) {
                    $output[]    = ['type' => 'line', 'content' => implode('', $currentLine)];
                    $currentLine = [];
                }
                $output[] = ['type' => 'line', 'content' => $part['content']];
            } else {
                $currentLine[] = $part['content'];
            }
        }

        if (!empty($currentLine)) {
            $output[] = ['type' => 'line', 'content' => implode('', $currentLine)];
        }

        // Second pass: build string
        $result       = '';
        $contentLines = [];

        foreach ($output as $item) {
            if ($item['type'] === 'container') {
                if (!empty($contentLines)) {
                    $lineBreak = $this->htmlMode ? '<br>' : "\n";
                    $result .= implode($lineBreak, $contentLines);
                    $contentLines = [];
                }
                $result .= $item['content'];
            } else {
                $content = $item['content'];

                if ($this->autoClean && !(strpos($content, '<div') === 0 || strpos($content, '</div>') === 0)) {
                    $content = $this->cleanLine($content);
                }

                if (trim($content) !== '') {
                    $contentLines[] = $content;
                }
            }
        }

        if (!empty($contentLines)) {
            $lineBreak = $this->htmlMode ? '<br>' : "\n";
            $result .= implode($lineBreak, $contentLines);
        }

        return $result;
    }

    /**
     * Build and return multiple strings as array, splitting at split() points
     */
    public function buildSeparate() {
        $results = [];

        // Add all finalized sections
        foreach ($this->sections as $section) {
            if ($this->hasValue($section)) {
                $results[] = $section;
            }
        }

        // Add current parts as final section if any
        if (!empty($this->parts)) {
            $current = $this->buildCurrentParts();

            if ($this->hasValue($current)) {
                $results[] = $current;
            }
        }

        return $results;
    }

    /**
     * Build separate strings and combine with specified separator
     */
    public function buildCombined($separator = '<br>') {
        $parts = $this->buildSeparate();

        if (empty($parts)) {
            return '';
        }

        $filteredParts = array_filter($parts, function($part) {
            return $this->hasValue($part);
        });

        return implode($separator, $filteredParts);
    }

    /**
     * Reset sections (useful for reusing the same formatter instance)
     */
    public function resetSections() {
        $this->sections       = [];
        $this->currentSection = 0;

        return $this;
    }
}
