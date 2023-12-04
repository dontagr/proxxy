<?php
namespace App\Command;

use App\Entity\Proxy;
use App\Enums\ProxyStatus;
use App\Enums\ProxyType;
use App\Service\IpApiClient;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

#[AsCommand(
    name: 'app:proxy-check',
    description: 'Checking some proxy.',
)]
class ProxyWorkerCommand extends Command
{
    private const CURL_OPT = [
        CURLOPT_URL => 'https://www.leomax.ru',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 OPR/105.0.0.0 (Edition Yx 05)',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 7,
        CURLOPT_CONNECTTIMEOUT => 7
    ];

    private Container $container;
    private EntityManager $em;
    private IpApiClient $ipApiClient;

    protected function configure(): void
    {
        $this->setName('proxy checker')
            ->setDescription('command for scan proxy info');
    }

    protected function init(): void
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->ipApiClient = $container->get(IpApiClient::class);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Start");
        $this->init();

        do {
            $proxy = $this->updateProxy($output);
        } while ($proxy !== null);

        $output->writeln("Finish");

        return Command::SUCCESS;
    }

    protected function updateProxy(OutputInterface $output): ?Proxy
    {
        $proxy = $this->getUnCheckProxy();
        if (!$proxy) {
            return null;
        }

        $output->writeln(sprintf(
            "get proxy [id=%s, taskId=%s, ip=%s, port=%s]",
            $proxy->getId(),
            $proxy->getTask()?->getId(),
            $proxy->getIp(),
            $proxy->getPort()
        ));

        $ipApiResponce = $this->ipApiClient->getInfoByProxy($proxy);
        $type = $this->getTypeByProxy($proxy);
        $proxy
            ->setStatus($type ? ProxyStatus::SUCCESS : ProxyStatus::FAIL)
            ->setProp($ipApiResponce->asArray())
            ->setType($type)
        ;
        $this->em->flush();

        return $proxy;
    }

    protected function getUnCheckProxy(): ?Proxy
    {
        $this->em->beginTransaction();
        $query = $this->em->createQuery(sprintf(
            'SELECT p FROM %s AS p WHERE p.status = :status AND p.locking = 0 ORDER BY p.id ASC',
            Proxy::class
        ));

        $query->setParameter("status", ProxyStatus::UNCHECK);
        $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
        $query->setMaxResults(1);

        /** @var Proxy $proxy */
        $proxy = $query->getOneOrNullResult();
        if (!$proxy) {
            return null;
        }

        $proxy->setLocking(1);
        $this->em->flush();
        $this->em->commit();

        return $proxy;
    }

    /** TODO refactor */
    protected function getTypeByProxy(Proxy $proxy): int
    {
        $ip = sprintf('%s:%s', $proxy->getIp(), $proxy->getPort());
        $chHTTP = curl_init();
        curl_setopt_array($chHTTP, self::CURL_OPT + [
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
            CURLOPT_PROXY => $ip
        ]);

        $chHTTPS = curl_init();
        curl_setopt_array($chHTTPS, self::CURL_OPT + [
            CURLOPT_PROXYTYPE => CURLPROXY_HTTPS,
            CURLOPT_PROXY => $ip
        ]);

        $chSOCKS4 = curl_init();
        curl_setopt_array($chSOCKS4, self::CURL_OPT + [
            CURLOPT_PROXYTYPE => CURLPROXY_SOCKS4,
            CURLOPT_PROXY => $ip
        ]);

        $chSOCKS5 = curl_init();
        curl_setopt_array($chSOCKS4, self::CURL_OPT + [
            CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
            CURLOPT_PROXY => $ip
        ]);

        $mh = curl_multi_init();
        curl_multi_add_handle($mh, $chHTTP);
        curl_multi_add_handle($mh, $chHTTPS);
        curl_multi_add_handle($mh, $chSOCKS4);
        curl_multi_add_handle($mh, $chSOCKS5);

        $running = 0;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        curl_multi_remove_handle($mh, $chHTTP);
        curl_multi_remove_handle($mh, $chHTTPS);
        curl_multi_remove_handle($mh, $chSOCKS4);
        curl_multi_remove_handle($mh, $chSOCKS5);
        curl_multi_close($mh);

        $respHTTP = curl_multi_getcontent($chHTTP);
        $respHTTPs = curl_multi_getcontent($chHTTPS);
        $respSOCKS4 = curl_multi_getcontent($chSOCKS4);
        $respSOCKS5 = curl_multi_getcontent($chSOCKS5);

        $type = ProxyType::NONE;
        if ($respHTTP) {
            $type = ProxyType::HTTP;
        } elseif ($respHTTPs) {
            $type = ProxyType::HTTP;
        } elseif ($respSOCKS4) {
            $type = ProxyType::SOCKS;
        } elseif ($respSOCKS5) {
            $type = ProxyType::SOCKS;
        }

        return $type;
    }
}