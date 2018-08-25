<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

/**
 * Caches values in process memory.
 */
final class ProcessMemory implements MemoryInterface
{
    /** @var array */
    private $data = [];

    /**
     * {@inheritdoc}
     */
    public function loadData(string $section)
    {
        return isset($this->data[$section]) ? $this->data[$section] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function saveData(string $section, $data)
    {
        $this->data[$section] = $data;
    }
}