<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Core;

/**
 * Nullable memory interface (does not save or load anything).
 */
final class NullMemory implements MemoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadData(string $section)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function saveData(string $section, $data)
    {
        //Nothing to do
    }
}