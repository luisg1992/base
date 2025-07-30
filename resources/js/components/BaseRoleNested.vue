<script setup>
import draggable from 'vuedraggable'
import Chip from "primevue/chip";
import Button from "primevue/button";

const props = defineProps(['children'])
const emit = defineEmits(['success', 'click-edit', 'click-delete', 'click-new', 'click-action-new', 'click-action-delete'])

const finish = () => {
    emit('success', props.children)
}

const clickModuleNew = (val) => {
    emit('click-new', val)
}

const clickModuleEdit = (val) => {
    emit('click-edit', val)
}

const clickModuleDelete = (val) => {
    emit('click-delete', val)
}

const clickActionNew = (val) => {
    emit('click-action-new', val)
}

const clickActionDelete = (val) => {
    emit('click-action-delete', val)
}

</script>

<template>
    <draggable class="dragArea"
               tag="ul"
               :list="children"
               :group="{ name: 'g1' }"
               item-key="ModuloId"
               handle=".handle"
               @end="finish">
        <template #item="{ element }">
            <li>
                <div style="display: flex; align-items: center" class="x-module-hover-icon x-div-module">
                    <div class="flex column q-ml-md" style="width: 100%; padding: 4px 0;">
                        <div>{{ element.Etiqueta }}</div>
                        <div>
                            <Chip v-for="accion in element.acciones"
                                  style="margin-right: 8px; margin-top: 4px; margin-bottom: 4px; padding: 4px 8px 4px 16px; font-size: 13px;">
                                <span>{{ accion.Nombre }}</span>
                                <i class="ti ti-x" style="cursor: pointer" @click="clickActionDelete(accion)"></i>
                            </Chip>
                        </div>
                    </div>
                </div>
                <base-role-nested :children="element.children"/>
            </li>
        </template>
    </draggable>
</template>
