<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Admins','route'=>'../gestion_adm','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'../add_admin','class'=>'','icon'=>'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Admins','route'=>'../gestion_adm','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'../add_admin','class'=>'','icon'=>'fas fa-user-plus'],
])]); ?>

    <div class="container">
        
        
        <h1 class="text-success my-3">Editer Administrateur:</h1>
        <?php if(session('update_success')): ?>
            <p id="update_msg"><?php echo e(session('update_success')); ?></p>
        <?php endif; ?>
        <div class="card shadow p-4 mt-5">
            <form action="<?php echo e(route('update_admin', $administrateur->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" value="<?php echo e($administrateur->name); ?>" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" value="<?php echo e($administrateur->email); ?>" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="etablissement" class="form-label">Etablissement</label>
                    <input type="text" value="<?php echo e($administrateur->etablissement); ?>" class="form-control" id="etablissement" name="etablissement" required>
                </div>


                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Mis a jour</button>
                </div>
            </form>
        </div>

    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/supadmin/edit_adm.blade.php ENDPATH**/ ?>