<?php

namespace Faker\Tests\Provider;

use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Fakecar;
use Faker\Provider\CarData;
use PHPUnit\Framework\TestCase;

class FakecarTest extends TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    public function setUp()
    {
        $faker = Factory::create();
        $faker->addProvider(new Fakecar($faker));
        $this->faker = $faker;
    }

    public function getProtectedProperty( $property, $class = null )
    {
        if( is_null($class))
        {
            $class = new Fakecar($this->faker);
        }

        $reflection = new \ReflectionClass($class);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);

        return $reflection_property->getValue($class, $property);
    }

    public function testVehicle()
    {
        $this->faker->seed(random_int(1, 9999));

        $vehicleBrand = $this->faker->vehicleBrand();

        $vehicleText = $this->faker->vehicle();
        $brands = CarData::getBrandsWithModels();

        foreach($brands as $brand => $models)
        {
            if(substr($vehicleText, 0, strlen($brand)) === $brand) {
                foreach ($models as $model)
                {
                    if(substr($vehicleText,  -strlen($model)) === $model) {

                        $this->assertStringEndsWith($model, $vehicleText);
                        break;
                    }
                }
            }
        }
    }

    public function testVehicleArray()
    {
        $vehicleArray = $this->faker->vehicleArray();

        $this->assertArrayHasKey('brand', $vehicleArray);
        $this->assertArrayHasKey('model', $vehicleArray);

        $brandsArray = CarData::getBrandsWithModels();

        $this->assertTrue(
            in_array(
                $vehicleArray['model'],
                $brandsArray[$vehicleArray['brand']]
            )
        );

    }

    public function testVehicleBrand()
    {
        $this->assertTrue(
            array_key_exists(
                $this->faker->vehicleBrand,
                CarData::getBrandsWithModels()
            )
        );
    }

    public function testVehicleModel($make = null)
    {
        $this->faker->seed(random_int(1, 9999));

        $modelArray = CarData::getBrandsWithModels();
        $modelArray = $modelArray[$this->faker->vehicleBrand()];

        $vehicleBrand = $this->faker->vehicleBrand();

        $this->assertTrue(
            in_array(
                $this->faker->vehicleModel($vehicleBrand),
                (CarData::getBrandsWithModels())[$vehicleBrand]
            )
        );
    }

    public function testVehicleRegistration()
    {
        $this->assertRegExp('/[A-Z]{3}-[0-9]{3}/', $this->faker->vehicleRegistration());
        $this->assertRegExp('/[A-Z]{2}-[0-9]{5}/', $this->faker->vehicleRegistration('[A-Z]{2}-[0-9]{5}'));
    }

    public function testVehicleType()
    {
        $this->assertTrue(in_array($this->faker->vehicleType, CarData::getVehicleTypes()));
    }

    public function testVehicleFuelType()
    {
        $this->assertTrue(in_array($this->faker->vehicleFuelType, CarData::getVehicleFuelTypes()));
    }

    public function testVehicleDoorCount()
    {
        for($i = 0; $i<10; $i++)
        {
            $this->assertThat(
                $this->faker->vehicleDoorCount,
                $this->logicalAnd(
                    $this->isType('int'),
                    $this->greaterThanOrEqual(2),
                    $this->lessThanOrEqual(6)
                )
            );
        }
    }

    public function testVehicleSeatCount()
    {
        for($i = 0; $i<10; $i++)
        {
            $this->assertThat(
                $this->faker->vehicleSeatCount,
                $this->logicalAnd(
                    $this->isType('int'),
                    $this->greaterThanOrEqual(1),
                    $this->lessThanOrEqual(9)
                )
            );
        }
    }

    public function testVehicleProperties()
    {
        $properties = $this->faker->vehicleProperties;
        $this->assertTrue(is_array($properties));

        $properties = $this->faker->vehicleProperties(2);
        $this->assertTrue(is_array($properties));
        $this->assertCount(2, $properties);

        $properties = $this->faker->vehicleProperties(5);
        $this->assertTrue(is_array($properties));
        $this->assertCount(5, $properties);

        //If we pass 0 we should get a random
        $properties = $this->faker->vehicleProperties(0);
        $this->assertTrue(is_array($properties));
        $this->assertGreaterThanOrEqual(0, count($properties));
    }

    public function testVehicleGearBox()
    {
        $this->assertTrue(in_array($this->faker->vehicleGearBoxType, CarData::getVehicleGearBoxType()));
    }

    public function testGetWeighted()
    {
        $data = [
            'key1' => 80,
            'key2' => 19,
            'key3' => 1,
        ];

        $result = array_fill_keys(array_keys($data), 0);

        for($i = 0; $i<1000; $i++)
        {
            $result[$this->faker->getWeighted($data)]++;
        }

        $this->assertGreaterThan($result['key2'], $result['key1']);
        $this->assertGreaterThan($result['key3'], $result['key2']);
        $this->assertGreaterThan($result['key3'], $result['key1']);

        $this->assertEquals('', $this->faker->getWeighted([]));
    }
}
