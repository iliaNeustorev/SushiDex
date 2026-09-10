<?php

namespace App\Helpers\Spattie\CustomSort;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;
use Spatie\QueryBuilder\Sorts\Sort;

readonly class SortByFieldRelation implements Sort
{

    public function __construct(
        private string $relationName
    ) {
    }

    public function __invoke(Builder|\Illuminate\Database\Eloquent\Builder $query, bool $descending, string $property)
    {
        $model = $query->getModel();

        if (!method_exists($model, $this->relationName)) {
            throw new InvalidArgumentException(
                "Relation $this->relationName does not exist on $model."
            );
        }

        $relation = Relation::noConstraints(
            fn() => $model->{$this->relationName}()
        );

        if (!$relation instanceof HasOne &&
            !$relation instanceof BelongsTo) {
            throw new InvalidArgumentException(
                'SortByRelationField supports only HasOne and BelongsTo relations.'
            );
        }

        $relatedModel = $relation->getRelated();
        $column = $relatedModel->qualifyColumn($property);

        $subquery = $relation->getRelationExistenceQuery(
            $relatedModel->newQuery(),
            $query,
            $column,
        );

        $query->orderBy(
            $subquery,
            $descending ? 'desc' : 'asc',
        );
    }
}
