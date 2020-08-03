<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\FailureInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\WarningInterface;
use Oro\Bundle\HealthCheckBundle\Helper\LoggerReporter;
use Psr\Log\LoggerInterface;

class LoggerReporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    /**
     * @var LoggerReporter
     */
    private $reporter;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->reporter = new LoggerReporter($this->logger);
    }

    public function testOnAfterRunWithOtherResult()
    {
        /** @var CheckInterface|\PHPUnit\Framework\MockObject\MockObject $check */
        $check = $this->createMock(CheckInterface::class);
        /** @var ResultInterface|\PHPUnit\Framework\MockObject\MockObject $result */
        $result = $this->createMock(ResultInterface::class);
        $check->expects($this->never())
            ->method('getLabel');
        $result->expects($this->never())
            ->method('getMessage');
        $result->expects($this->never())
            ->method('getData');
        $this->logger->expects($this->never())
            ->method('error');
        $this->logger->expects($this->never())
            ->method('warning');

        $this->reporter->onAfterRun($check, $result);
    }

    public function testOnAfterRunWithWarning()
    {
        /** @var CheckInterface|\PHPUnit\Framework\MockObject\MockObject $check */
        $check = $this->createMock(CheckInterface::class);
        /** @var WarningInterface|\PHPUnit\Framework\MockObject\MockObject $result */
        $result = $this->createMock(WarningInterface::class);
        $check->expects($this->once())
            ->method('getLabel')
            ->willReturn('test label');
        $result->expects($this->once())
            ->method('getMessage')
            ->willReturn('test message');
        $result->expects($this->never())
            ->method('getData');
        $this->logger->expects($this->never())
            ->method('error');
        $this->logger->expects($this->once())
            ->method('warning')
            ->with('HEALTHCHECK: "test label". Message: "test message"');

        $this->reporter->onAfterRun($check, $result);
    }

    public function testOnAfterRunWithFailure()
    {
        /** @var CheckInterface|\PHPUnit\Framework\MockObject\MockObject $check */
        $check = $this->createMock(CheckInterface::class);
        /** @var FailureInterface|\PHPUnit\Framework\MockObject\MockObject $result */
        $result = $this->createMock(FailureInterface::class);
        $check->expects($this->once())
            ->method('getLabel')
            ->willReturn('test label');
        $result->expects($this->once())
            ->method('getMessage')
            ->willReturn('test message');
        $exception = new \Exception();
        $result->expects($this->once())
            ->method('getData')
            ->willReturn($exception);
        $this->logger->expects($this->once())
            ->method('error')
            ->with('HEALTHCHECK: "test label". Message: "test message"', $exception->getTrace());
        $this->logger->expects($this->never())
            ->method('warning');

        $this->reporter->onAfterRun($check, $result);
    }
}
