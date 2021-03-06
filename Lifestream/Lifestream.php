<?php

namespace Lyrixx\Lifestream;

use Lyrixx\Lifestream\Service\ServiceInterface;
use Lyrixx\Lifestream\Formatter\FormatterInterface;
use Lyrixx\Lifestream\Filter\FilterInterface;

/**
 * Class Lifestream is the engine of Lifestream library.
 */
class Lifestream
{
    private $service;
    private $filters    = array();
    private $formatters = array();
    private $stream;
    private $isBooted;

    /**
     * Constructor
     *
     * @param ServiceInterface $service    A service
     * @param array            $filters    An array of filter
     * @param array            $formatters An array of formatter
     * @param StreamInterface  $stream     A stream
     */
    public function __construct(ServiceInterface $service = null, array $filters = array(), array $formatters = array(), StreamInterface $stream = null)
    {
        $this->service = $service;
        $this->setFilters($filters);
        $this->setFormatters($formatters);
        $this->stream = $stream ?: new Stream();
        $this->isBooted = false;
    }

    /**
     * Fetch datas, filter and format them.
     *
     * @return Lifestream $this
     */
    public function boot()
    {
        if (null === $this->service) {
            throw new \LogicException('You must setup a ServiceInterface');
        }

        foreach ($this->service->getStatuses() as $status) {
            $hasToContinue = false;
            foreach ($this->filters as $filter) {
                if (!$filter->isValid($status)) {
                    $hasToContinue = true;

                    break;
                }
            }

            if ($hasToContinue) {
                continue;
            }

            foreach ($this->formatters as $formatter) {
                $status = $formatter->format($status);
            }

            $this->stream->addStatus($status);
        }

        $this->isBooted = true;

        return $this;
    }

    /**
     * Return the service
     *
     * @return ServiceInterface The service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set the service
     *
     * @param ServiceInterface $service The service
     */
    public function setService(ServiceInterface $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Add a filter
     *
     * @param FilterInterface $filter A filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Set a collection of filter
     * They must implement FilterInterface
     *
     * @param array $filters A collection of filter
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * Add a formatter
     *
     * @param FormatterInterface $formatter A formatter
     */
    public function addFormatter(FormatterInterface $formatter)
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    /**
     * Set a collection of formatter
     * They must implement FormatterInterface
     *
     * @param array $formatters A collection of formatter
     */
    public function setFormatters(array $formatters)
    {
        foreach ($formatters as $formatter) {
            $this->addFormatter($formatter);
        }

        return $this;
    }

    /**
     * Return a StreamInterface with all Statuses
     *
     * Lifestream::boot() must be called before this method.
     *
     * @throws \LogicException If lifestream is not booted
     *
     * @return StreamInterface The Stream
     */
    public function getStream()
    {
        if (!$this->isBooted) {
            throw new \LogicException('You must boot Lifestream before try to get stream');
        }

        return $this->stream;
    }
}
