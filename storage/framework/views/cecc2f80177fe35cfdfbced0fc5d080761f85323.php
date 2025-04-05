<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Admins','route'=>'gestion_formateur','class'=>'active','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'add_admin','class'=>'','icon'=>'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Admins','route'=>'gestion_formateur','class'=>'active','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'add_admin','class'=>'','icon'=>'fas fa-user-plus'],
])]); ?>
    

    <div class="container-fluid px-4">
        <?php if(session('delete_success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('delete_success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-users-cog me-3 fs-4"></i>
                <h1 class="card-title mb-0 fs-5">Gestion des Administrateurs</h1>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">
                                    <i class="fas fa-hashtag me-2"></i>ID
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-user me-2"></i>Name
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-envelope me-2"></i>Login
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-building me-2"></i>Institution
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-cogs me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $administrateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $administrateur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center align-middle"><?php echo e($administrateur->id); ?></td>
                                    <td class="text-center align-middle"><?php echo e($administrateur->name); ?></td>
                                    <td class="text-center align-middle"><?php echo e($administrateur->email); ?></td>
                                    <td class="text-center align-middle"><?php echo e($administrateur->etablissement); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?php echo e(route('edit_adm',$administrateur)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="<?php echo e(route('delete_admin', $administrateur->id)); ?>" method="post" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this administrator?')">
                                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($administrateurs->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Optional: Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.classList.add('table-active');
                });
                row.addEventListener('mouseleave', function() {
                    this.classList.remove('table-active');
                });
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/supadmin/gestion_admin.blade.php ENDPATH**/ ?>