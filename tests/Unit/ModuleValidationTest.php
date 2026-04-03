<?php

use App\Models\Module;

//Test # Sistem can create a module
it('can create module', function () {
    $module = new Module([
        'title'=>'example',
        'description' => 'example',
        'due_date'=>'2026-04-01',
        'competition_id'=>1,
    ]);

    expect($module)->toBeInstanceOf(Module::class);
    expect($module->competition_id)->toBe(1);
});

//Test # Sistem can change data of the module
it('can change module data', function() {
    $module = new Module([
        'title'=>'example',
        'description' => 'example',
        'due_date'=>'2026-04-01',
        'competition_id'=>1,        
    ]);

    $module->fill([
        'title'=>'new example',
        'description'=>'new description',
        'competition_id'=>2,
        'due_date'=>"2026-05-01"
    ]);

    expect($module->title)->toBe("new example");
    expect($module->description)->toBe("new description");
    expect($module->competition_id)->toBe(2);
    expect($module->due_date)->toBe("2026-05-01");
});

//Test # Sistem can create a module's timestamps
it('can create timestamps to module', function () {
    $module = new Module([
        'title'=>'example',
        'description' => 'example',
        'due_date'=>'2026-04-01',
        'competition_id'=>1,
    ]);

    expect($module->timestamps)->toBeTrue();
    expect($module->getCreatedAtColumn())->toBe('created_at');
    expect($module->getUpdatedAtColumn())->toBe('updated_at');
});