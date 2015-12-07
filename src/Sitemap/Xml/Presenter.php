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
	}

	public function actionView(
		int $id = NULL,
		int $page = 0
	) {
		$this->properties = array_filter(
			$this->linkRepository->getEntityMetadata()->getProperties(),
			function (Nextras\Orm\Entity\Reflection\PropertyMetadata $propertyMetadata) {
				return $propertyMetadata->relationship && $propertyMetadata->relationship->type === Nextras\Orm\Entity\Reflection\PropertyRelationshipMetadata::ONE_HAS_ONE;
			}
		);
		$this->document = new DOMDocument(
			'1.0',
			'UTF-8'
		);
		$id === NULL ? $this->generateIndex() : $this->generateSitemap(
			$id,
			$page
		);
		$this->getHttpResponse()->setContentType('application/xml');
		$this->sendResponse(new Nette\Application\Responses\TextResponse($this->document->saveXML()));
	}

	private function generateIndex()
	{
		$domain = $this->web->domains->get()->getBy(['host' => $this->domain]);
		if ( ! $domain instanceof Ytnuk\Web\Domain\Entity) {
			$this->error();
		}
		$index = $this->document->appendChild(
			$this->document->createElementNS(
				'http://www.sitemaps.org/schemas/sitemap/0.9',
				'sitemapindex'
			)
		);
		array_walk(
			$this->properties,
			function (Nextras\Orm\Entity\Reflection\PropertyMetadata $metadata) use
			(
				$index
			) {
				$repository = $this->model->getRepository($metadata->relationship->repository);
				$paginator = (
				new Ytnuk\Orm\Pagination\Control(
					$repository->findAll(),
					self::ITEMS_PER_PAGE
				)
				)->getPaginator();
				$id = $this->getPropertyId($metadata);
				foreach (
					range(
						$paginator->getFirstPage(),
						$paginator->getLastPage()
					) as $page
				) {
					$sitemap = $index->appendChild($this->document->createElement('sitemap'));
					$sitemap->appendChild(
						$this->document->createElement(
							'loc',
							$this->link(
								'//this',
								[
									'id' => $id,
									'page' => $page,
								]
							)
						)
					);
					$sitemap->appendChild(
						$this->document->createElement(
							'lastmod',
							(new DateTime)->format('c')
						)
					);
				}
			}
		);
	}

	private function generateSitemap(
		int $id,
		int $page
	) {
		$properties = array_combine(
			array_map(
				[
					$this,
					'getPropertyId',
				],
				$this->properties
			),
			$this->properties
		);
		$property = $properties[$id] ? : NULL;
		if ( ! $property instanceof Nextras\Orm\Entity\Reflection\PropertyMetadata) {
			$this->error();
		}
		$pagination = new Ytnuk\Orm\Pagination\Control(
			$this->model->getRepository($property->relationship->repository)->findAll(),
			self::ITEMS_PER_PAGE
		);
		if ($pagination->getPaginator()->setPage($page)->getPage() !== $page) {
			$this->error();
		}
		$urlset = $this->document->appendChild(
			$this->document->createElementNS(
				'http://www.sitemaps.org/schemas/sitemap/0.9',
				'urlset'
			)
		);
		$collection = iterator_to_array($pagination);
		array_walk(
			$collection,
			function (Nextras\Orm\Entity\IEntity $entity) use
			(
				$property,
				$urlset
			) {
				//TODO: use parent cache to determine lastmod for whole sitemap
				//TODO: cache each url element, so lastmod will always be up to date
				//TODO: maybe make use of components to cache everything, same way as product/post categories
				$url = $urlset->appendChild($this->document->createElement('url'));
				$url->appendChild(
					$this->document->createElement(
						'loc',
						$this->link(
							$entity->getValue($property->relationship->property),
							['absolute' => TRUE]
						)
					)
				);
				$url->appendChild(
					$this->document->createElement(
						'lastmod',
						(new DateTime)->format('c')
					)
				);
			}
		);
	}

	private function getPropertyId(Nextras\Orm\Entity\Reflection\PropertyMetadata $metadata) : int
	{
		return crc32($metadata->name);
	}
}
