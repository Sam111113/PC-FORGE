<?php

namespace App\Tests\Service;

use App\Service\BuildSessionManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BuildSessionManagerTest extends TestCase
{
    private SessionInterface&MockObject $session;
    private BuildSessionManager $manager;
    private const SESSION_KEY = 'userBuild';

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($this->session);

        $this->manager = new BuildSessionManager($requestStack);
    }

    private function emptyBuild(): array
    {
        return [
            'cpuId' => null, 'mbId' => null, 'gpuId' => null,
            'ramId' => null, 'storageId' => null, 'coolerId' => null,
            'psuId' => null, 'boitierId' => null, 'fanId' => null,
        ];
    }

    private function fullBuild(): array
    {
        return [
            'cpuId' => 1, 'mbId' => 2, 'gpuId' => 3,
            'ramId' => 4, 'storageId' => 5, 'coolerId' => 6,
            'psuId' => 7, 'boitierId' => 8, 'fanId' => 9,
        ];
    }
    public function testInitBuild(): void
    {
        $this->session->method('get')->willReturn(null);
        $state = $this->manager->initBuild();

        $this->assertEquals($this->emptyBuild(), $state);
    }

    public function testInitBuildExistant(): void
    {
        $existing = $this->fullBuild();
        $this->session->method('get')->willReturn($existing);

        $state = $this->manager->initBuild();

        $this->assertEquals($existing, $state);
    }

    public function testGetBuild(): void
    {
        $expected = $this->fullBuild();
        $this->session->method('get')->willReturn($expected);

        $this->assertEquals($expected, $this->manager->getBuild());
    }

    public function testSaveBuild(): void
    {
        $state = $this->fullBuild();

        $this->session->expects($this->once())
            ->method('set')
            ->with(self::SESSION_KEY, $state);

        $this->manager->saveBuild($state);
    }

    public function testSetComponent(): void
    {
        $build = $this->emptyBuild();
        $this->session->method('get')->willReturn($build);

        $expected = $build;
        $expected['cpuId'] = 5;

        $this->session->expects($this->once())
            ->method('set')
            ->with(self::SESSION_KEY, $expected);

        $this->manager->setComponent('cpuId', 5);
    }

    public function testGetComponent(): void
    {
        $this->session->method('get')->willReturn($this->fullBuild());

        $this->assertEquals(1, $this->manager->getComponent('cpuId'));
    }

    public function testGetComponentNull(): void
    {
        $this->session->method('get')->willReturn($this->emptyBuild());

        $this->assertNull($this->manager->getComponent('cpuId'));
    }

    public function testHasComponent(): void
    {
        $this->session->method('get')->willReturn($this->fullBuild());

        $this->assertTrue($this->manager->hasComponent('cpuId'));
    }

    public function testHasComponentFalse(): void
    {
        $this->session->method('get')->willReturn($this->emptyBuild());

        $this->assertFalse($this->manager->hasComponent('cpuId'));
    }

public function testRemovePart(): void
{
    $build = $this->fullBuild();
    $this->session->method('get')->willReturn($build);

    $expected = $build;
    $expected['psuId'] = null;
    $expected['boitierId'] = null;
    $expected['fanId'] = null;

    $this->session->expects($this->once())
        ->method('set')
        ->with(self::SESSION_KEY, $expected);

    $this->manager->removePart('psuId');
}

    public function testResetBuild(): void
    {
        $this->session->expects($this->once())
            ->method('remove')
            ->with(self::SESSION_KEY);

        $this->manager->resetBuild();
    }

    public function testIsBuildComplete(): void
    {
        $this->session->method('get')->willReturn($this->fullBuild());

        $this->assertTrue($this->manager->isBuildComplete());
    }

    public function testIsBuildNotComplete(): void
    {
        $this->session->method('get')->willReturn($this->emptyBuild());

        $this->assertFalse($this->manager->isBuildComplete());
    }
}
