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
	 * @var Nextras\Orm\Model\IModel
	 */
	private $model;

	/**
	 * @var Ytnuk\Link\Repository
	 */
	private $linkRepository;

	/**
	 * @var array|Nextras\Orm\Entity\Reflection\PropertyMetadata[]
	 */
	private $properties = [];

	/**
	 * @var Nextras\Orm\Entity\Reflection\PropertyMetadata
	 */
	private $property;

	/**
	 * @var DOMDocument
	 */
	private $document;

	public function __construct(
		Nextras\Orm\Model\IModel $model,
		Ytnuk\Link\Repository $linkRepository
	) {
		parent::__construct();
		$this->model = $model;
		$this->linkRepository = $linkRepository;
		$this->document = new DOMDocument('1.0', 'UTF-8');
	}

	//TODO: rewrite from other side (one sided relations) of link entity metadata
	public function actionView(int $id = NULL)
	{
		$this->properties = array_combine(array_map('crc32', array_column($this->properties = array_filter($this->linkRepository->getEntityMetadata()->getProperties(), function (Nextras\Orm\Entity\Reflection\PropertyMetadata $propertyMetadata) {
			return $propertyMetadata->relationship && $propertyMetadata->relationship->type === Nextras\Orm\Entity\Reflection\PropertyRelationshipMetadata::ONE_HAS_ONE;
		}), 'name')), $this->properties);
		$id === NULL ? $this->generateIndex() : $this->generateUrlSet($id);
	}

	public function renderView()
	{
		$this->getHttpResponse()->setContentType('application/xml');
		$this->sendResponse(new Nette\Application\Responses\TextResponse($this->document->saveXML()));
	}

	protected function createComponentPagination() : Ytnuk\Orm\Pagination\Control
	{
		if ( ! $this->property) {
			$this->error();
		}

		return new Ytnuk\Orm\Pagination\Control($this->model->getRepository($this->property->relationship->repository)->findAll(), self::ITEMS_PER_PAGE);
	}

	private function generateIndex()
	{
		$domain = $this->web->domains->get()->getBy(['host' => $this->domain]);
		if ( ! $domain instanceof Ytnuk\Web\Domain\Entity) {
			$this->error();
		}
		$index = $this->document->appendChild($this->document->createElementNS(self::NAMESPACE_URI, 'sitemapindex'));
		array_walk($this->properties, function (
			Nextras\Orm\Entity\Reflection\PropertyMetadata $metadata,
			int $id
		) use
		(
			$index
		) {
			$this->property = $metadata;
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

	private function generateUrlSet(
		int $id
	) {
		if ( ! $this->property = $this->properties[$id] ?? NULL) {
			$this->error();
		}
		$urlSet = $this->document->appendChild($this->document->createElementNS(self::NAMESPACE_URI, 'urlset'));
		$collection = iterator_to_array($this['pagination']);
		array_walk($collection, function (Nextras\Orm\Entity\IEntity $entity) use
		(
			$urlSet
		) {
			//TODO: use parent cache to determine lastmod for whole sitemap
			//TODO: cache each url element, so lastmod will always be up to date
			$url = $urlSet->appendChild($this->document->createElement('url'));
			$url->appendChild($this->document->createElement('loc', $this->link($entity->getValue($this->property->relationship->property), ['absolute' => TRUE])));
			$url->appendChild($this->document->createElement('lastmod', (new DateTime)->format('c')));
		});
	}
}
