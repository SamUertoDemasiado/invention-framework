<?php


namespace OSN\Framework\Testing;


use Throwable;

trait ChecksIfDevServerIsListening
{
    /**
     * @test
     */
    public function testApplicationDevServerIsListening()
    {
        try {
            $sock = $this->sockopen();

            if (!$sock) {
                echo "You must start the development server to test the app. Run `php console serve' and try again.\n";
                exit(-1);
            }
            else {
                $this->assertSame(true, is_resource($sock));
            }
        }
        catch (Throwable $e) {
            echo "You must start the development server to test the app. Run `php console serve' and try again.\n";
            exit(-1);
        }
    }

    protected function sockopen()
    {
        return fsockopen('localhost', env('SERVER_PORT'));
    }
}
