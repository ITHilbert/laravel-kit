<?php

namespace ITHilbert\LaravelKit\Tests\Unit;

use Tests\TestCase;

class ResponseMacrosTest extends TestCase
{
    public function test_api_success_macro()
    {
        $response = response()->apiSuccess(['id' => 1], 'Data loaded', 200);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Data loaded', $responseData['message']);
        $this->assertEquals(1, $responseData['data']['id']);
    }

    public function test_api_error_macro()
    {
        $response = response()->apiError('Validation failed', 422, ['field' => 'required']);
        
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Validation failed', $responseData['message']);
        $this->assertEquals('required', $responseData['errors']['field']);
    }

    public function test_api_no_content_macro()
    {
        $response = response()->apiNoContent();
        
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }
}
