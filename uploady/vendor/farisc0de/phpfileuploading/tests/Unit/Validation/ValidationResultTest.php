<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;
use Farisc0de\PhpFileUploading\Validation\ValidationError;

class ValidationResultTest extends TestCase
{
    public function testSuccessCreatesValidResult(): void
    {
        $result = ValidationResult::success();

        $this->assertTrue($result->isValid());
        $this->assertFalse($result->isFailed());
        $this->assertFalse($result->hasErrors());
    }

    public function testSuccessWithMetadata(): void
    {
        $result = ValidationResult::success(['key' => 'value']);

        $this->assertTrue($result->isValid());
        $this->assertEquals(['key' => 'value'], $result->getMetadata());
    }

    public function testFailureCreatesInvalidResult(): void
    {
        $result = ValidationResult::failure('Error message', 'ERROR_CODE');

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isFailed());
        $this->assertTrue($result->hasErrors());
    }

    public function testAddErrorMakesResultInvalid(): void
    {
        $result = ValidationResult::success();
        $result->addError('Something went wrong', 'ERROR');

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testAddWarningDoesNotAffectValidity(): void
    {
        $result = ValidationResult::success();
        $result->addWarning('This is a warning', 'WARNING');

        $this->assertTrue($result->isValid());
        $this->assertTrue($result->hasWarnings());
        $this->assertCount(1, $result->getWarnings());
    }

    public function testGetFirstError(): void
    {
        $result = ValidationResult::failure('First error', 'E1');
        $result->addError('Second error', 'E2');

        $this->assertEquals('First error', $result->getFirstError());
    }

    public function testGetFirstErrorReturnsNullWhenNoErrors(): void
    {
        $result = ValidationResult::success();

        $this->assertNull($result->getFirstError());
    }

    public function testGetErrorMessages(): void
    {
        $result = ValidationResult::failure('Error 1', 'E1');
        $result->addError('Error 2', 'E2');

        $messages = $result->getErrorMessages();

        $this->assertCount(2, $messages);
        $this->assertContains('Error 1', $messages);
        $this->assertContains('Error 2', $messages);
    }

    public function testMergeResults(): void
    {
        $result1 = ValidationResult::success(['key1' => 'value1']);
        $result2 = ValidationResult::failure('Error from result2', 'E1');
        $result2->addMetadata('key2', 'value2');

        $result1->merge($result2);

        $this->assertFalse($result1->isValid());
        $this->assertCount(1, $result1->getErrors());
        $this->assertEquals('value1', $result1->getMetadataValue('key1'));
        $this->assertEquals('value2', $result1->getMetadataValue('key2'));
    }

    public function testToArray(): void
    {
        $result = ValidationResult::failure('Test error', 'TEST_CODE', ['context' => 'value']);

        $array = $result->toArray();

        $this->assertFalse($array['valid']);
        $this->assertCount(1, $array['errors']);
        $this->assertEquals('Test error', $array['errors'][0]['message']);
        $this->assertEquals('TEST_CODE', $array['errors'][0]['code']);
    }

    public function testToJson(): void
    {
        $result = ValidationResult::success(['test' => true]);

        $json = $result->toJson();
        $decoded = json_decode($json, true);

        $this->assertTrue($decoded['valid']);
        $this->assertEquals(['test' => true], $decoded['metadata']);
    }
}
