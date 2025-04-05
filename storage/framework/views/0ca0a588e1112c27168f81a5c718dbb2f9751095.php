<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Formateurs','route'=>'../gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'../gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'../add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Formateurs','route'=>'../gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'../gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'../add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
])]); ?>
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-success">Suivi d'avancement du formateur: <?php echo e($teacher->name); ?></h1>
                    <a href="<?php echo e(route('export.weekly_progress', ['id' => $teacher->id])); ?>" class="btn btn-primary">Export Weekly Progress</a>
                </div>
            
                <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
            
                <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
            
                <!-- Progress Overview Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Modules</h5>
                                <h2><?php echo e(count($modules)); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Modules à jour</h5>
                                <h2><?php echo e(count(array_filter($modules, function($m) { return $m['status'] === 'À jour'; }))); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Modules en retard</h5>
                                <h2><?php echo e(count(array_filter($modules, function($m) { return $m['status'] === 'En retard'; }))); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Progression moyenne</h5>
                                <h2>
                                    <?php echo e(count($modules) > 0 ? 
                                        round(array_sum(array_column($modules, 'completion_percentage')) / count($modules), 1) : 
                                        0); ?>%
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Modules Progress Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="card-title">Détails d'avancement par module</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Module</th>
                                        <th>Groupe</th>
                                        <th>Filière</th>
                                        <th>Type</th>
                                        <th>Progression</th>
                                        <th>Heures (Faites/Total)</th>
                                        <th>Date début</th>
                                        <th>Date Examen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($module['module_code']); ?></strong>
                                            <div class="small"><?php echo e($module['module_name']); ?></div>
                                        </td>
                                        <td><?php echo e($module['group_name']); ?></td>
                                        <td><?php echo e($module['fillier_name']); ?></td>
                                        <td><?php echo e($module['type_seance']==='totale'?"distanciel/presentiel":$module['type_seance']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;background-color:rgb(160, 151, 151);">
                                                <div class="progress-bar 
                                                    <?php if($module['status'] == 'En retard'): ?> bg-danger 
                                                    <?php elseif($module['status'] == 'Terminé'): ?> bg-success 
                                                    <?php else: ?> bg-primary <?php endif; ?>" 
                                                    role="progressbar" 
                                                    style="width: <?php echo e($module['completion_percentage']); ?>%;"
                                                    aria-valuenow="<?php echo e($module['completion_percentage']); ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <label for="" class="">
                                                <?php echo e($module['completion_percentage']); ?>%
                                            </label>
                                        </td>
                                        <td><?php echo e($module['completed_hours']); ?> / <?php echo e($module['total_hours']); ?> h</td>
                                        <td><?php echo e($module['start_date']); ?></td>
                                        <td><?php echo e($module['exam_date']); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Aucun module assigné à ce formateur</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/admin/progress.blade.php ENDPATH**/ ?>