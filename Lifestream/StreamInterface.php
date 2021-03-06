<?php

namespace Lyrixx\Lifestream;

/**
 * StreamInterface represents a collection of StatusInterface
 */
interface StreamInterface extends \Countable, \Iterator
{
    /**
     * Add a StatusInterface to the stream
     *
     * @param StatusInterface $status The StatusInterface
     */
    public function addStatus(StatusInterface $status);
}
