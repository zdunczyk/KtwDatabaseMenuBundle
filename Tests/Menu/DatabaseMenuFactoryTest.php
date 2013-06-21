<?php

namespace kevintweber\KtwDatabaseMenuBundle\Tests\Menu;

use kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem;
use kevintweber\KtwDatabaseMenuBundle\Menu\DatabaseMenuFactory;

/**
 * DatabaseMenuFactory tests.
 *
 * Since kevintweber\KtwDatabaseMenuBundle\Menu\DatabaseMenuFactory inherits
 * from Knp\Menu\Silex\RouterAwareFactory, I have copied many of the tests
 * from /Knp/Menu/Tests/MenuFactoryTest.php and
 * Knp\Menu\Tests\Silex\RouterAwareFactoryTest.php to here. Therefore most of
 * these tests are thanks to stof of KNP Labs.  Thank you.
 */
class DatabaseMenuFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArrayWithoutChildren()
    {
       $urlGeneratorInterfaceMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($urlGeneratorInterfaceMock,
                                           $containerInterfaceMock);
        $array = array(
            'name' => 'joe',
            'uri' => '/foobar',
            'display' => false,
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEquals('/foobar', $item->getUri());
        $this->assertFalse($item->isDisplayed());
        $this->assertEmpty($item->getAttributes());
        $this->assertEmpty($item->getChildren());
    }

    public function testFromArrayWithChildren()
    {
       $urlGeneratorInterfaceMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($urlGeneratorInterfaceMock,
                                           $containerInterfaceMock);
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'name' => 'jack',
                    'label' => 'Jack',
                ),
                array(
                    'name' => 'john'
                )
            ),
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
    }

    public function testFromArrayWithChildrenOmittingName()
    {
       $urlGeneratorInterfaceMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($urlGeneratorInterfaceMock,
                                           $containerInterfaceMock);
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'label' => 'Jack',
                ),
                'john' => array(
                    'label' => 'John'
                )
            ),
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
        $this->assertTrue(isset($item['jack']));
    }

    public function testCreateItemWithRoute()
    {
        $generatorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generatorMock->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), false)
            ->will($this->returnValue('/foobar'))
        ;


        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($generatorMock,
                                           $containerInterfaceMock);

        $item = $factory->createItem('test_item', array('uri' => '/hello', 'route' => 'homepage'));
        $this->assertEquals('/foobar', $item->getUri());
    }

    public function testCreateItemWithRouteAndParameters()
    {
        $generatorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generatorMock->expects($this->once())
            ->method('generate')
            ->with('homepage', array('id' => 12), false)
            ->will($this->returnValue('/foobar'))
        ;

        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($generatorMock,
                                           $containerInterfaceMock);

        $item = $factory->createItem('test_item', array('route' => 'homepage', 'routeParameters' => array('id' => 12)));
        $this->assertEquals('/foobar', $item->getUri());
    }

    public function testCreateItemWithAbsoluteRoute()
    {
        $generatorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generatorMock->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), true)
            ->will($this->returnValue('http://php.net'))
        ;

        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($generatorMock,
                                           $containerInterfaceMock);

        $item = $factory->createItem('test_item', array('route' => 'homepage', 'routeAbsolute' => true));
        $this->assertEquals('http://php.net', $item->getUri());
    }

    public function testCreateItemAppendsRouteUnderExtras()
    {
        $generatorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $containerInterfaceMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerInterfaceMock->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('kevintweber\KtwDatabaseMenuBundle\Entity\MenuItem'));

        $factory = new DatabaseMenuFactory($generatorMock,
                                           $containerInterfaceMock);

        $item = $factory->createItem('test_item', array('route' => 'homepage'));
        $this->assertEquals(array('homepage'), $item->getExtra('routes'));

        $item = $factory->createItem('test_item', array('route' => 'homepage', 'extras' => array('routes' => array('other_page'))));
        $this->assertContains('homepage', $item->getExtra('routes'));
    }
}