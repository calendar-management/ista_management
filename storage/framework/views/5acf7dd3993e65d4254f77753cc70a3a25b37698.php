<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'add_formateur','class'=>'active','icon'=>'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'add_formateur','class'=>'active','icon'=>'fas fa-user-plus'],
])]); ?>
<?php if(session("add_frm_success")): ?>
    <script>
        alert("<?php echo e(session("add_frm_success")); ?>")
    </script>
<?php endif; ?>
    <div class="container">
        <h1 class="text-success my-3">Ajouter Nouveau Formateur:</h1>
        

        <div class="card shadow p-4 mt-5">
            <form action="<?php echo e(route('add_formateur')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" value="<?php echo e(old('name')); ?>" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="matricule" class="form-label">Matricule</label>
                    <input type="text" class="form-control" value="<?php echo e(old('matricule')); ?>" id="matricule" name="matricule" required>
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Login</label>
                    <input type="text" class="form-control" value="<?php echo e(old('email')); ?>" id="email" name="email" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-danger ml-3">Ce login est déjà utilisé par un autre utilisateur</p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/admin/add_frm.blade.php ENDPATH**/ ?>