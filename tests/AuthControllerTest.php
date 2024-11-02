<?php
use PHPUnit\Framework\TestCase;
use App\core\modules\auth\AuthController;
use App\core\modules\auth\AuthUseCase;
use App\core\modules\auth\dto\LoginResponseDTO;

class AuthControllerTest extends TestCase
{
    private $authController;
    private $authUseCaseMock;

    protected function setUp(): void
    {
        // Mock the AuthUseCase dependency
        $this->authUseCaseMock = $this->createMock(AuthUseCase::class);
        $this->authController = new AuthController($this->authUseCaseMock);
    }

    // Test for login method
    public function testLoginSuccessful()
    {
        // Set up mock response
        $loginResponse = new LoginResponseDTO(true, 'Login successful', 'mock_access_token', 'mock_refresh_token');
        $this->authUseCaseMock->method('login')->willReturn($loginResponse);

        // Input data
        $requestData = ['email' => 'testing@gmail.com', 'password' => 'pass1'];

        // Capture output
        ob_start();
        $this->authController->login($requestData);
        $output = ob_get_clean();

        echo $output, PHP_EOL;

        // Assertions
        $this->assertStringContainsString('Login successful', $output);
        $this->assertStringContainsString('mock_access_token', $output);
    }

    public function testLoginInvalidInput()
    {
        // Simulate missing fields in input
        $requestData = ['email' => 'testing@gmail.com'];

        // Capture output
        ob_start();
        $this->authController->login($requestData);
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Invalid input', $output);
    }

    // Test for refreshAccessToken method
    public function testRefreshAccessTokenSuccessful()
    {
        // Simulate valid refresh token
        $_COOKIE['r'] = 'valid_refresh_token';
        $this->authUseCaseMock->method('refreshAccessToken')->willReturn('new_access_token');

        // Capture output
        ob_start();
        $this->authController->refreshAccessToken();
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('new_access_token', $output);
    }

    public function testRefreshAccessTokenUnauthorized()
    {
        // Simulate missing refresh token
        unset($_COOKIE['r']);

        // Capture output
        ob_start();
        $this->authController->refreshAccessToken();
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Unauthorized', $output);
    }

    public function testRefreshAccessTokenInvalidToken()
    {
        // Simulate invalid or expired refresh token
        $_COOKIE['r'] = 'invalid_token';
        $this->authUseCaseMock->method('refreshAccessToken')->willReturn(null);

        // Capture output
        ob_start();
        $this->authController->refreshAccessToken();
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Invalid or expired refresh token', $output);
    }

    // Test for logout method
    public function testLogoutSuccessful()
    {
        // Set up mock response for successful logout
        $this->authUseCaseMock->method('logout')->willReturn(true);

        // Input data for logout
        $requestData = ['refreshToken' => 'valid_refresh_token'];

        // Capture output
        ob_start();
        $this->authController->logout($requestData);
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Successfully logged out', $output);
    }

    public function testLogoutInvalidInput()
    {
        // Missing refreshToken in request
        $requestData = [];

        // Capture output
        ob_start();
        $this->authController->logout($requestData);
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Invalid input', $output);
    }

    public function testLogoutInvalidToken()
    {
        // Simulate invalid or revoked refresh token
        $this->authUseCaseMock->method('logout')->willReturn(false);

        // Input data for logout
        $requestData = ['refreshToken' => 'revoked_token'];
        ob_flush();
        // Capture output
        ob_start();
        $this->authController->logout($requestData);
        $output = ob_get_clean();
        echo $output, PHP_EOL;
        // Assertions
        $this->assertStringContainsString('Invalid or already revoked refresh token', $output);
    }
}
