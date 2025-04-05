<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'active','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label' => 'Ajouter Formateur', 'route' => 'add_formateur', 'class' => '', 'icon' => 'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'active','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label' => 'Ajouter Formateur', 'route' => 'add_formateur', 'class' => '', 'icon' => 'fas fa-user-plus'],
])]); ?>
            <div class="container-fluid">
                <?php if(session('import_success')): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?php echo e(session('import_success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h1 class="text-success m-3">Gestion Des Formateurs:</h1>
                    </div>
                    
                    <div class="border p-4 m-2 m-md-4 rounded shadow bg-light">
                        <a href="<?php echo e(url('/download/AvancementProgramme_exemple.xlsx')); ?>" class="btn btn-primary m-3">
                            <i class="fas fa-download me-2"></i>Télécharger un exemple de fichier
                        </a>
                        <div class="row align-items-center">
                            
                            <div class="col-12 d-md-none my-3 border-bottom"></div>

                            <div class="col-md-1 d-none d-md-block">
                                <div class="border-start h-100"></div>
                            </div>

                            <div class="col-md-6 text-center text-md-start">
                                <form id="importForm" action="<?php echo e(route('import_file')); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <label for="data" class="form-label fw-bold">Import Fichier Excel:</label>
                                    <input type="file" name="data" id="data" class="form-control btn btn-primary"
                                        style="padding-bottom: 2.25rem;">
                                    <button type="submit" id="importButton" class="btn btn-primary m-3">
                                        <i class="fas fa-file-import me-2"></i>Import
                                    </button>
                                </form>

                                <!-- Progress bar container (hidden by default) -->
                                <div id="progressContainer" class="mt-3" style="display: none;">
                                    <div class="progress" style="height: 25px;">
                                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                            style="width: 0%">0%</div>
                                    </div>
                                    <p id="statusMessage" class="mt-2">Préparation du fichier...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    

                    <div class="mb-3 search">
                        <form action="<?php echo e(route('formateurs.search')); ?>" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher un formateur..."
                                    value="<?php echo e(request('search')); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formateur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($formateur->matricule); ?></td>
                                        <td><?php echo e($formateur->name); ?></td>
                                        <td><?php echo e($formateur->email); ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="<?php echo e(route('edit_formateur',$formateur->id)); ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="<?php echo e(route('teacher.progress', $formateur->id)); ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-line"></i> Suivre
                                                </a>
                                                <!-- Suppression -->
                                                <form action="<?php echo e(route('formateurs.destroy', $formateur->id)); ?>" method="POST" class="d-inline" 
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <?php echo e($formateurs->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        
        <!-- Dark overlay when sidebar is active (mobile) -->
        <div class="overlay"></div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/admin/gestion_formateur.blade.php ENDPATH**/ ?>