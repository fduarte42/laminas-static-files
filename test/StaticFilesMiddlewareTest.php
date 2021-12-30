<?php
declare(strict_types=1);

namespace Fduarte42\StaticFilesTest;

require_once __DIR__ . '/../src/ContentTypes.php';

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Fduarte42\StaticFiles\StaticFilesMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

class StaticFilesMiddlewareTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRefusesToReturnFileThatIsOutsideTheAssetDirectoryForSecurity()
    {
        $unit = new StaticFilesMiddleware(__DIR__ . '/public-test');
        $request = new ServerRequest([], [], 'https://example.com/../secrets.php', 'GET');
        $responseFromDelegate = new Response\EmptyResponse();

        /** @var RequestHandlerInterface|MockObject $mockRequestHandler */
        $mockRequestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $mockRequestHandler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($responseFromDelegate);

        $responseFromUnit = $unit->process($request, $mockRequestHandler);

        $this->assertTrue($responseFromDelegate === $responseFromUnit);
    }

    /**
     * @throws Exception
     */
    public function testReturnsFileContentAndProperHeadersWhenFileExistsAndIsValid()
    {
        $unit = new StaticFilesMiddleware(__DIR__ . '/public-test');
        $request = new ServerRequest([], [], 'https://example.com/test.json', 'GET');

        /** @var RequestHandlerInterface|MockObject $mockRequestHandler */
        $mockRequestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        /**
         * @var $responseFromUnit ResponseInterface
         */
        $responseFromUnit = $unit->process( $request, $mockRequestHandler );

        $expectedFileContents = file_get_contents(__DIR__ . '/public-test/test.json');

        $responseFromUnit->getBody()->rewind();
        $this->assertEquals(
            $expectedFileContents,
            $responseFromUnit->getBody()->getContents()
        );

        $this->assertEquals(
            $responseFromUnit->getHeaders(),
            ['content-type' => ['application/json']]
        );
    }

    /**
     * @throws Exception
     */
    public function testIgnorePhpFiles()
    {
        $unit = new StaticFilesMiddleware(__DIR__ . '/public-test3');
        $request = new ServerRequest([], [], 'https://example.com/index.php', 'GET');

        $responseFromDelegate = new Response\EmptyResponse();

        /** @var RequestHandlerInterface|MockObject $mockRequestHandler */
        $mockRequestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $mockRequestHandler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($responseFromDelegate);

        $responseFromUnit = $unit->process($request, $mockRequestHandler);

        $this->assertTrue($responseFromDelegate === $responseFromUnit);
    }

    /**
     * @throws Exception
     */
    public function testMultipleAssetDirectories()
    {
        $filesSystemAssetDirectories = [
            __DIR__ . '/public-test2',
            __DIR__ . '/public-test'
        ];

        $unit = new StaticFilesMiddleware($filesSystemAssetDirectories);
        $request = new ServerRequest([], [], 'https://example.com/test2.json', 'GET');

        /** @var RequestHandlerInterface|MockObject $mockRequestHandler */
        $mockRequestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        /**
         * @var $responseFromUnit ResponseInterface
         */
        $responseFromUnit = $unit->process($request, $mockRequestHandler);

        $expectedFileContents = file_get_contents(__DIR__ . '/public-test2/test2.json');

        $responseFromUnit->getBody()->rewind();
        $this->assertEquals(
            $expectedFileContents,
            $responseFromUnit->getBody()->getContents()
        );

        $this->assertEquals(
            $responseFromUnit->getHeaders(),
            ['content-type' => ['application/json']]
        );
    }

    /**
     * @throws Exception
     */
    public function testMultipleAssetDirectoriesWithOverride()
    {
        $filesSystemAssetDirectories = [
            __DIR__ . '/public-test',
            __DIR__ . '/public-test2',
        ];

        $unit = new StaticFilesMiddleware($filesSystemAssetDirectories);
        $request = new ServerRequest([], [], 'https://example.com/test.json', 'GET');

        /** @var RequestHandlerInterface|MockObject $mockRequestHandler */
        $mockRequestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        /**
         * @var $responseFromUnit ResponseInterface
         */
        $responseFromUnit = $unit->process($request, $mockRequestHandler);

        $expectedFileContents = file_get_contents(__DIR__ . '/public-test2/test.json');

        $responseFromUnit->getBody()->rewind();
        $this->assertEquals(
            $expectedFileContents,
            $responseFromUnit->getBody()->getContents()
        );

        $this->assertEquals(
            $responseFromUnit->getHeaders(),
            ['content-type' => ['application/json']]
        );
    }
}
