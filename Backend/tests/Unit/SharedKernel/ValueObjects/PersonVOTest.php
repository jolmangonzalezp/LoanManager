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

        $person = PersonVO::create($name, $dni, $phone, $address);

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

        $person = PersonVO::create($name, $dni, $phone, $address, $email);

        expect($person->getEmail())->toEqual($email);
    });

    it('returns null for optional email not set', function () {
        $name = NameVO::create('Juan', 'García', 'López');
        $dni = DniVO::create('123456789', DniType::CC);
        $phone = PhoneVO::create('3001234567');
        $address = AddressVO::create('Calle 123, Bogotá');

        $person = PersonVO::create($name, $dni, $phone, $address);

        expect($person->getEmail())->toBeNull();
    });

    it('compares equality correctly', function () {
        $name1 = NameVO::create('Juan', 'García', 'López');
        $dni1 = DniVO::create('123456789', DniType::CC);
        $phone1 = PhoneVO::create('3001234567');
        $address1 = AddressVO::create('Calle 123, Bogotá');
        $person1 = PersonVO::create($name1, $dni1, $phone1, $address1);

        $name2 = NameVO::create('Juan', 'García', 'López');
        $dni2 = DniVO::create('123456789', DniType::CC);
        $phone2 = PhoneVO::create('3001234567');
        $address2 = AddressVO::create('Calle 123, Bogotá');
        $person2 = PersonVO::create($name2, $dni2, $phone2, $address2);

        $name3 = NameVO::create('Pedro', 'García', 'López');
        $dni3 = DniVO::create('123456789', DniType::CC);
        $phone3 = PhoneVO::create('3001234567');
        $address3 = AddressVO::create('Calle 123, Bogotá');
        $person3 = PersonVO::create($name3, $dni3, $phone3, $address3);

        expect($person1->equals($person2))->toBeTrue();
        expect($person1->equals($person3))->toBeFalse();
    });
});