<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
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