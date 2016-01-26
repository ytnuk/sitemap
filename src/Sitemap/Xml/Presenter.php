<?php
namespace Ytnuk\Sitemap\Xml;

use DateTime;
use DOMDocument;
use Nette;
use Nextras;
use Ytnuk;

final class Presenter
	extends Ytnuk\Web\Application\Presenter
{

	const ITEMS_PER_PAGE = 666;
	const NAMESPACE_URI = 'http://www.sitemaps.org/schemas/sitemap/0.9';

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * @var Nextras\Orm\Repository\IRepository
	 */
	private $repository;

	/**
	 * @var DOMDocument
	 */
	private $document;

	public function __construct(
		Nette\DI\Container $container
	) {
		parent::__construct();
		$this->container = $container;
		$this->document = new DOMDocument('1.0', 'UTF-8');
	}

	private function getLinkProperties(Nextras\Orm\Repository\IRepository $repository)
	{
		return array_filter($repository->getEntityMetadata()->getProperties(), function (Nextras\Orm\Entity\Reflection\PropertyMetadata $propertyMetadata) {
			return $propertyMetadata->relationship && ($propertyMetadata->relationship->entity === Ytnuk\Link\Entity::class || is_subclass_of($propertyMetadata->relationship->entity, Ytnuk\Link\Entity::class)) && $propertyMetadata->relationship->type === Nextras\Orm\Entity\Reflection\PropertyRelationshipMetadata::ONE_HAS_ONE;
		});
	}

	public function actionView(int $id = NULL)
	{
		$repositories = array_combine(array_map('crc32', array_map('get_class', $repositories = array_map([
			$this->container,
			'getService',
		], $this->container->findByType(Nextras\Orm\Repository\IRepository::class)))), $repositories);
		if ($id === NULL) {
			$this->generateIndex(array_filter($repositories, [
				$this,
				'getLinkProperties',
			]));
		} elseif (isset($repositories[$id])) {
			$this->generateUrlSet($this->repository = $repositories[$id]);
		} else {
			$this->error();
		}
	}

	public function renderView()
	{
		$this->getHttpResponse()->setContentType('application/xml');
		$this->sendResponse(new Nette\Application\Responses\TextResponse($this->document->saveXML()));
	}

	protected function createComponentPagination() : Ytnuk\Orm\Pagination\Control
	{
		if ( ! $this->repository) {
			$this->error();
		}

		return new Ytnuk\Orm\Pagination\Control($this->repository->findAll(), self::ITEMS_PER_PAGE);
	}

	private function generateIndex(array $repositores)
	{
		$domain = $this->web->domains->get()->getBy(['host' => $this->domain]);
		if ( ! $domain instanceof Ytnuk\Web\Domain\Entity) {
			$this->error();
		}
		$index = $this->document->appendChild($this->document->createElementNS(self::NAMESPACE_URI, 'sitemapindex'));
		array_walk($repositores, function (
			Nextras\Orm\Repository\IRepository $repository,
			int $id
		) use
		(
			$index
		) {
			$this->repository = $repository;
			$this->params['id'] = $id;
			$pagination = $this['pagination'];
			$paginator = $pagination->getPaginator();
			foreach (
				range($paginator->getFirstPage(), $paginator->getLastPage()) as $page
			) {
				$sitemap = $index->appendChild($this->document->createElement('sitemap'));
				$sitemap->appendChild($this->document->createElement('loc', $pagination->link('//this', ['page' => $page])));
				$sitemap->appendChild($this->document->createElement('lastmod', (new DateTime)->format('c')));
			}
			unset($this['pagination']); //mischief managed
		});
	}

	private function generateUrlSet(Nextras\Orm\Repository\IRepository $repository)
	{
		if ( ! $properies = $this->getLinkProperties($repository)) {
			$this->error();
		}
		$urlSet = $this->document->appendChild($this->document->createElementNS(self::NAMESPACE_URI, 'urlset'));
		$collection = iterator_to_array($this['pagination']);
		array_walk($collection, function (Nextras\Orm\Entity\IEntity $entity) use
		(
			$properies,
			$urlSet,
			$repository
		) {
			//TODO: use parent cache to determine lastmod for whole sitemap
			//TODO: cache each url element, so lastmod will always be up to date
			array_walk($properies, function (Nextras\Orm\Entity\Reflection\PropertyMetadata $propertyMetadata) use
			(
				$entity,
				$urlSet,
				$repository
			) {
				$url = $urlSet->appendChild($this->document->createElement('url'));
				$url->appendChild($this->document->createElement('loc', $this->link($entity->getValue($propertyMetadata->name), ['absolute' => TRUE])));
				$url->appendChild($this->document->createElement('lastmod', (new DateTime)->format('c')));
			});
		});
	}
}
