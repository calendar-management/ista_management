<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar','data' => ['navlinks' => [
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'active','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navlinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'active','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
])]); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/vacances.js'); ?>
<?php if(session("ajouter_succ")): ?>
    <script>
        alert("<?php echo e(session("ajouter_succ")); ?>")
    </script>
<?php endif; ?>
    <div class="text-center">
        <h1 class="text-success m-3">Gestion Des Vacances:</h1>

        <div class="container">
            <div class="call">
                <div class="cal-scroll col-md-12">
                    <div id='calendar'></div>
                    <div style='clear:both'></div>

                </div>
                <form action="<?php echo e(route('add_vacances')); ?>" method="POST" id="holidayFormContainer">
                    <?php echo csrf_field(); ?>
                    <div class="add-holiday-form p-3 border rounded">
                        <h4>Ajouter Un Nouvel Evénement</h4>
                        <div class="form-group">
                            <label for="eventType">Select Type:</label>
                            <select id="eventType" name="eventType" class="form-control">
                                <option value="vacance">Vacance</option>
                                <option value="stage">Stage</option>
                                <option value="regional">Regional</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="holidayStartDate">Date De Début:</label>
                            <input type="date" id="holidayStartDate" name="holidayStartDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="holidayEndDate">Date De Fin:</label>
                            <input type="date" id="holidayEndDate" name="holidayEndDate" class="form-control" >
                        </div>
                        <div class="form-group" id="dynamicField"></div>
                        <div class="form-group">
                            <label for="holidayDescription">Description:</label>
                            <input type="text" id="holidayDescription" name="holidayDescription" class="form-control"
                                placeholder="Enter event description">
                        </div>
                        <button type="submit" id="addHolidayBtn" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>




            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\school\stage\calendar_ista\calendar_ista\ista_management\resources\views/admin/gestion_calendrier.blade.php ENDPATH**/ ?>