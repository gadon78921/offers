## Получить предложения для сервиса поиска. Апи является временным, после перехода на новую витрину должно быть удалено

`POST /offers-temp/get-by-assortment-unit-ids`

#### Параметры
##### Обязательные параметры:

| Поле              | Тип    | Описание                 |
|:------------------|:------:|:-------------------------|
| assortmentUnitIds | int[]  | Id ассортиментных позици |
| kladrId           | string | Кладр                    |

#### Ответ

Код `200`: список предложений

Содержимое ответа:

| Поле   | Тип   | Описание           |
|:-------|:-----:|:-------------------|
| result | array | Список предложений |

Пример:

```json
{
  "result": [
    {
      "assortmentUnitId": 311811,
      "price": 233.0,
      "priceWithDiscount": 170.0,
      "priceForPreorder": 170.0,
      "priceForWaiting": 150.0,
      "discount": 27,
      "discountForPreorder": 27,
      "discountForWaiting": 35,
      "wholesalePrice": 107.02,
      "isFixedDiscount": false
    },
    ...
  ]
}
``` 