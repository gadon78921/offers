<?php

declare(strict_types=1);

namespace App\ProductsApi\Controller;

use App\ProductsApi\Repository\CatalogSeoAttributesDatabaseAccessObject;
use App\ProductsApi\Repository\KaisProductsDatabaseAccessObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly KaisProductsDatabaseAccessObject $dao,
        private readonly CatalogSeoAttributesDatabaseAccessObject $catalogSeoAttributesDao,
        private readonly string $offersImagesUrl,
    ) {}

    /**
     * @param list<int> $retailProductIds
     *
     * @return list<array{
     *      'productId'              : int,
     *      'retailProductCode'      : int,
     *      'assortmentUnitId'       : int,
     *      'kaisProductId'          : int,
     *      'baseProductIdInUrl'     : int,
     *      'productName'            : string|null,
     *      'manufacturerName'       : string|null,
     *      'manufacturerCountryName': string|null,
     *      'storageConditions'      : string|null,
     *      'sellProcedure'          : string|null,
     *      'isVital'                : bool,
     *      'indicatorForUse'        : string|null,
     *      'contraindicationsForUse': string|null,
     *      'pharmodynamic'          : string|null,
     *      'pharmokinetic'          : string|null,
     *      'sideEffect'             : string|null,
     *      'methodOfUse'            : string|null,
     *      'composition'            : string|null,
     *      'fullDescription'        : string|null,
     *      'expirationDate'         : int|null,
     *      'packageName'            : string|null,
     *      'packageQuantity'        : int|null,
     *      'dosage'                 : string|null,
     *      'dosageForm'             : string|null,
     *      'activeSubstance'        : string|null,
     *      'productDescription'     : string|null,
     *      'categoriesName'         : string|null,
     *      'categoryId'             : int|null,
     *      'breadcrumb'             : string|null,
     *      'fullName'               : string|null,
     *      'isStorageTypeCold'      : int,
     *      'prescriptionForm'       : int,
     *      'seoAttributes'          : array{'pageTitle': string, 'description': string},
     *      'categoriesName'         : list<string>,
     *      'breadcrumb'             : list<array{'id': string, 'title': string, 'slug': string}>,
     *      'imagePath'              : string,
     *      'imageExt'               : string,
     *      'imageUpdatedAt'         : null,
     *      'pharmGroup'             : null,
     *      'maxGNVLSPrice'          : null,
     *      'drugForm'               : null,
     *      'brandName'              : string,
     *      'slug'                   : string
     *  }>
     */
    #[Route('/products/get-list-by-retail-product-ids', name: 'get-list-by-retail-product-ids')]
    #[ParamConverter('retailProductIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function getListByRetailProductIds(array $retailProductIds): array
    {
        $products             = $this->dao->getListByRetailProductIds($retailProductIds);
        $catalogSeoAttributes = $this->catalogSeoAttributesDao->fetchAll();

        return array_map(function (array $product) use ($catalogSeoAttributes) {
            $groupPath                 = null === $product['groupPath'] ? [] : json_decode($product['groupPath'], true, 512, JSON_THROW_ON_ERROR);
            $lastGroup                 = empty($groupPath) ? null : end($groupPath);
            $product['categoryId']     = empty($lastGroup) ? null : (int) $lastGroup['id'];
            $product['categoriesName'] = array_column($groupPath, 'title');

            $product['seoAttributes'] = $this->createSeoAttributes(
                $product['retailProductCode'],
                $product['tradeName'],
                $product['productDescription'],
                $product['dosage'],
                $catalogSeoAttributes[$product['categoryId']]['seoTitle'] ?? null,
                $catalogSeoAttributes[$product['categoryId']]['seoDescription'] ?? null,
            );

            $product['productId']      = $product['retailProductId'];
            $product['breadcrumb']     = $this->createBreadcrumb($groupPath);
            $product['imagePath']      = $this->offersImagesUrl . 'no/';
            $product['imageExt']       = 'png';
            $product['imageUpdatedAt'] = null;
            $product['pharmGroup']     = null;
            $product['maxGNVLSPrice']  = null;
            $product['drugForm']       = null;
            $product['brandName']      = '';
            $product['slug']           = '';

            unset($product['groupPath'], $product['tradeName'], $product['retailProductId']);

            return $product;
        }, $products);
    }

    /**
     * @return array{'pageTitle': string, 'description': string}
     */
    private function createSeoAttributes(
        int $retailProductCode,
        ?string $tradeName,
        ?string $productDescription,
        ?string $dosage,
        ?string $seoTitle,
        ?string $seoDescription,
    ): array {
        $search = [
            '/\\[tradeName\\]/i',
            '/\\[drugFormKindFullName\\]/i',
            '/\\[title\\]/i',
            '/\\[fullDosage\\]/i',
            '/\\[product_code\\]/i',
            '/\\[maxDiscount\\]/i',
        ];

        $replace = [
            $tradeName,
            !empty($productDescription) ? $productDescription : '',
            !empty($productDescription) ? $productDescription : '',
            !empty($dosage) ? $dosage : '',
            $retailProductCode,
            '50',
        ];

        $pageTitle   = empty($seoTitle) ? '' : preg_replace($search, $replace, $seoTitle);
        $description = empty($seoDescription) ? '' : preg_replace($search, $replace, $seoDescription);

        return [
            'pageTitle'   => $pageTitle,
            'description' => $description,
        ];
    }

    /**
     * @param list<array{'id': string, 'title': string, 'firstLvl': string, 'groupUrl': string, 'promoBreadcrumbsTitle': string, 'promoBreadcrumbsDisabled': string}> $groupPath
     *
     * @return list<array{'id': string, 'slug': string, 'title': string}>
     */
    private function createBreadcrumb(array $groupPath): array
    {
        foreach ($groupPath as $group) {
            $breadcrumb[] = [
                'id'    => $group['id'],
                'slug'  => $group['groupUrl'],
                'title' => $group['title'],
            ];
        }

        return $breadcrumb ?? [];
    }

    /**
     * @param list<int> $kaisProductIds
     *
     * @return list<array{string, int}>
     */
    #[Route('/products/get-retail-product-ids-by-kais-ids', name: 'get-retail-product-ids-by-kais-ids')]
    #[ParamConverter('kaisProductIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function getRetailProductIdsByKaisIds(array $kaisProductIds): array
    {
        return $this->dao->getRetailProductIdsByKaisIds($kaisProductIds);
    }
}
