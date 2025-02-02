<?php

namespace Tests;

use App\Models\Access\User;
use PHPUnit\Framework\Attributes\Test;

abstract class ItemTest extends TestCase
{
    protected $user;

    protected $dummyData = [];

    protected $dummyTranslatableData = [];

    protected $dummyAdditionalData = [];

    protected $validStructure;

    protected $uri;

    protected $class;

    protected $tableName;

    protected $translationsTableName;

    protected $pivot = false;

    protected $pivotAttribute = 'id';

    protected $searchAttribute = 'name';

    protected $searchString = null;

    protected $itemAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->afterCreating(function ($model) {
            $model->assignRole('root');
        })->create();
        $model = (new $this->class);
        $this->tableName = $model->getTable();
        if (method_exists($model, 'translations')) {
            $translationModelName = $model->getTranslationModelName();
            $this->translationsTableName = (new $translationModelName)->getTable();
        }
    }

    protected function prepareDummy(array $data): array
    {
        $newData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $newData[$key] = $this->castAsJson($value);
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }

    protected function createItem($attributes = [])
    {
        $item = call_user_func([$this->class, 'factory']);
        $this->actingAs($this->user);

        return $item->create($attributes);
    }

    #[Test]
    public function show_item()
    {
        $item = $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri.'/'.($this->pivot ? $item->{$this->pivotAttribute} : $item->id))
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['pivot' => $this->validStructure['data']]]
                : $this->validStructure);
    }

    #[Test]
    public function list_items()
    {
        $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri)
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]]);
    }

    #[Test]
    public function list_items_by_id()
    {
        $item = $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri.'/?id='.$item->id)
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]])
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function list_items_sorted_by_id()
    {
        $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri.'?sort_by=id&sort_desc=0')
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]]);
    }

    #[Test]
    public function search_item_by_id()
    {
        $item = $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->getJson($this->uri.'?search='.($this->pivot ? $item->{$this->pivotAttribute} : $item->id))
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]])
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function search_item_by_string()
    {
        $item = $this->createItem($this->itemAttributes);
        if (! $this->searchString) {
            $this->searchString = $item->{$this->searchAttribute};
            if (! $this->searchString) {
                $this->searchString = 'not_existing_string-123';
            }
        }
        $this->actingAs($this->user)
            ->getJson($this->uri.'?search='.$this->searchString)
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure($this->pivot
                ? ['data' => ['*' => ['pivot' => $this->validStructure['data']]]]
                : ['data' => ['*' => $this->validStructure['data']]]);
    }

    #[Test]
    public function create_item()
    {
        $dummyData = $this->dummyData;
        if (! empty($this->dummyAdditionalData)) {
            $dummyData = array_merge($dummyData, $this->dummyAdditionalData);
        }
        if (! empty($this->dummyTranslatableData)) {
            $dummyData = array_merge($dummyData, $this->dummyTranslatableData);
        }
        $response = $this->actingAs($this->user)
            ->postJson($this->uri, $dummyData)
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['pivot' => $this->validStructure['data']]]
                : $this->validStructure);

        $this->assertDatabaseHas($this->tableName, $this->prepareDummy($this->dummyData));

        if ($this->dummyTranslatableData) {
            foreach ($this->dummyTranslatableData as $field => $value) {
                $fieldParts = [];
                preg_match('/^([^:]+):([^$]+)$/', $field, $fieldParts);
                $dummyTranslatableData[$fieldParts[1]] = $value;
                $dummyTranslatableData['locale'] = $fieldParts[2];
                $this->assertDatabaseHas($this->translationsTableName, $this->prepareDummy($dummyTranslatableData));
            }
        }

        return $response;
    }

    #[Test]
    public function update_item()
    {
        $item = $this->createItem($this->itemAttributes);
        if ($this->pivot) {
            $this->dummyData[$this->pivotAttribute] = $item->{$this->pivotAttribute};
        }
        $dummyData = $this->dummyData;
        if (! empty($this->dummyAdditionalData)) {
            $dummyData = array_merge($dummyData, $this->dummyAdditionalData);
        }
        if (! empty($this->dummyTranslatableData)) {
            $dummyData = array_merge($dummyData, $this->dummyTranslatableData);
        }
        $response = $this->actingAs($this->user)
            ->putJson($this->uri.'/'.($this->pivot ? $item->{$this->pivotAttribute} : $item->id), $dummyData)
            ->assertSuccessful()
            ->assertJsonStructure($this->pivot
                ? ['data' => ['pivot' => $this->validStructure['data']]]
                : $this->validStructure);

        $this->assertDatabaseHas($this->tableName, $this->prepareDummy($this->dummyData));

        if ($this->dummyTranslatableData) {
            foreach ($this->dummyTranslatableData as $field => $value) {
                $fieldParts = [];
                preg_match('/^([^:]+):([^$]+)$/', $field, $fieldParts);
                $dummyTranslatableData[$fieldParts[1]] = $value;
                $dummyTranslatableData['locale'] = $fieldParts[2];
                $this->assertDatabaseHas($this->translationsTableName, $this->prepareDummy($dummyTranslatableData));
            }
        }

        return $response;
    }

    #[Test]
    public function delete_item()
    {
        $item = $this->createItem($this->itemAttributes);
        $this->actingAs($this->user)
            ->deleteJson($this->uri.'/'.($this->pivot ? $item->{$this->pivotAttribute} : $item->id))
            ->assertSuccessful();

        $this->assertDatabaseMissing($this->tableName, $this->prepareDummy($this->dummyData));

        if ($this->dummyTranslatableData) {
            foreach ($this->dummyTranslatableData as $field => $value) {
                $fieldParts = [];
                preg_match('/^([^:]+):([^$]+)$/', $field, $fieldParts);
                $dummyTranslatableData[$fieldParts[1]] = $value;
                $dummyTranslatableData['locale'] = $fieldParts[2];
                $this->assertDatabaseMissing($this->translationsTableName, $this->prepareDummy($dummyTranslatableData));
            }
        }
    }
}
