<?php
declare(strict_types=1);

namespace Kanti\LetsencryptClient;


use Exception;
use function Safe\sprintf;

final class NginxProxy
{
    /** @var ?string */
    private $dockerGenContainer;

    public function __construct()
    {
        $this->getDockerGenContainer();
    }

    public function reload(): void
    {
        $result = shell_exec(sprintf(
            "docker exec -it %s sh -c '/app/docker-entrypoint.sh /usr/local/bin/docker-gen /app/nginx.tmpl /etc/nginx/conf.d/default.conf; /usr/sbin/nginx -s reload'",
            $this->getDockerGenContainer()
        ));
        echo $result . PHP_EOL . 'Nginx Reloaded.' . PHP_EOL;
    }

    private function getDockerGenContainer(): string
    {
        if ($this->dockerGenContainer === null) {
            $result = shell_exec(sprintf('docker ps -f "label=com.github.kanti.local_https.nginx_proxy" -q'));
            if (!$result) {
                throw new Exception('ERROR NginxProxy Not found. did you not set the label=com.github.kanti.local_https.nginx_proxy on jwilder/nginx-proxy');
            }
            $this->dockerGenContainer = trim($result);
        }
        return $this->dockerGenContainer;
    }
}
