<?php

use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;
use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\SharedKernel\Domain\ValueObjects\NameVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\SharedKernel\Domain\ValueObjects\PhoneVO;

describe('PersonVO', function () {
    it('creates with required fields', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);

        expect($person->getName())->toEqual($name);
        expect($person->getDni())->toEqual($dni);
        expect($person->getPhone())->toEqual($phone);
        expect($person->getAddress())->toEqual($address);
    });

    it('creates with optional email', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');
        $email = EmailVO::create('juan@example.com');

        $person = new PersonVO($name, $dni, $phone, $address, $email);

        expect($person->getEmail())->toEqual($email);
    });

    it('returns null for optional email not set', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);

        expect($person->getEmail())->toBeNull();
    });

    it('creates with withEmail method', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);
        $newEmail = EmailVO::create('juan@example.com');
        $personWithEmail = $person->withEmail($newEmail);

        expect($person->getEmail())->toBeNull();
        expect($personWithEmail->getEmail())->toEqual($newEmail);
    });

    it('withName returns new instance', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);
        $newName = NameVO::create('Pedro', 'López', 'García');
        $personWithNewName = $person->withName($newName);

        expect($person->getFullName())->toBe('Juan García López');
        expect($personWithNewName->getFullName())->toBe('Pedro López García');
    });

    it('withDni returns new instance', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);
        $newDni = DniVO::create('87654321', DniType::CC);
        $personWithNewDni = $person->withDni($newDni);

        expect($person->getDni()->getNumber())->toBe('123456789');
        expect($personWithNewDni->getDni()->getNumber())->toBe('87654321');
    });

    it('withPhone returns new instance', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);
        $newPhone = PhoneVO::create('3009876543');
        $personWithPhone = $person->withPhone($newPhone);

        expect($personWithPhone->getPhone())->toEqual($newPhone);
    });

    it('withAddress returns new instance', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);
        $newAddress = AddressVO::create('Calle 456, Bogotá');
        $personWithAddress = $person->withAddress($newAddress);

        expect($personWithAddress->getAddress())->toEqual($newAddress);
    });

    it('compares equality correctly', function () {
        $name1 = NameVO::create('Juan', 'García', 'López');
        $dni1 = DniVO::create('123456789', DniType::CC);
        $phone1 = PhoneVO::create('3001234567');
        $address1 = AddressVO::create('Calle 123, Bogotá');
        $person1 = new PersonVO($name1, $dni1, $phone1, $address1);

        $name2 = NameVO::create('Juan', 'García', 'López');
        $dni2 = DniVO::create('123456789', DniType::CC);
        $phone2 = PhoneVO::create('3001234567');
        $address2 = AddressVO::create('Calle 123, Bogotá');
        $person2 = new PersonVO($name2, $dni2, $phone2, $address2);

        $name3 = NameVO::create('Pedro', 'García', 'López');
        $dni3 = DniVO::create('123456789', DniType::CC);
        $phone3 = PhoneVO::create('3001234567');
        $address3 = AddressVO::create('Calle 123, Bogotá');
        $person3 = new PersonVO($name3, $dni3, $phone3, $address3);

        expect($person1->equals($person2))->toBeTrue();
        expect($person1->equals($person3))->toBeFalse();
    });

    it('casts to string', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address);

        expect((string) $person)->toBe('Juan García López');
    });

    it('converts to array', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $email = EmailVO::create('juan@example.com');
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = new PersonVO($name, $dni, $phone, $address, $email);
        $array = $person->toArray();

        expect($array)->toBe([
            'name' => 'Juan García López',
            'dni' => '123.456.789',
            'email' => 'juan@example.com',
            'phone' => '+573001234567',
            'address' => 'Calle 123, Bogotá',
        ]);
    });
});
