<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Trash2, X, Calculator as CalcIcon } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Calculator', href: '/calculator' },
];

interface Calculation {
    id: number;
    expression: string;
    result: string;
    created_at: string;
}

const expression = ref('');
const result = ref<string | null>(null);
const error = ref<string | null>(null);
const history = ref<Calculation[]>([]);
const loading = ref(false);

const fetchHistory = async () => {
    try {
        const response = await fetch('/api/calculations');
        history.value = await response.json();
    } catch {
        error.value = 'Failed to load history';
    }
};

const calculate = async () => {
    if (!expression.value.trim()) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch('/api/calculations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ expression: expression.value }),
        });

        const data = await response.json();

        if (!response.ok) {
            error.value = data.error || 'Calculation failed';
            return;
        }

        result.value = data.result;
        history.value.unshift(data);
    } catch {
        error.value = 'Failed to calculate';
    } finally {
        loading.value = false;
    }
};

const deleteCalculation = async (id: number) => {
    try {
        await fetch(`/api/calculations/${id}`, { method: 'DELETE' });
        history.value = history.value.filter((c) => c.id !== id);
    } catch {
        error.value = 'Failed to delete';
    }
};

const clearAll = async () => {
    try {
        await fetch('/api/calculations', { method: 'DELETE' });
        history.value = [];
    } catch {
        error.value = 'Failed to clear history';
    }
};

const appendToExpression = (value: string) => {
    expression.value += value;
};

const clearExpression = () => {
    expression.value = '';
    result.value = null;
    error.value = null;
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') {
        calculate();
    }
};

onMounted(fetchHistory);
</script>

<template>
    <Head title="Calculator" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <CalcIcon class="size-5" />
                            Calculator
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Input
                                v-model="expression"
                                placeholder="Enter expression (e.g., 2+2, sqrt(16), 2^8)"
                                class="font-mono text-lg"
                                @keydown="handleKeydown"
                            />
                            <div
                                v-if="result !== null"
                                class="rounded-lg bg-muted p-3 text-right text-2xl font-bold"
                            >
                                = {{ result }}
                            </div>
                            <div
                                v-if="error"
                                class="rounded bg-destructive/10 p-2 text-sm text-destructive"
                            >
                                {{ error }}
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <Button
                                variant="outline"
                                @click="appendToExpression('7')"
                                >7</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('8')"
                                >8</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('9')"
                                >9</Button
                            >
                            <Button
                                variant="secondary"
                                @click="appendToExpression('/')"
                                >/</Button
                            >

                            <Button
                                variant="outline"
                                @click="appendToExpression('4')"
                                >4</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('5')"
                                >5</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('6')"
                                >6</Button
                            >
                            <Button
                                variant="secondary"
                                @click="appendToExpression('*')"
                                >*</Button
                            >

                            <Button
                                variant="outline"
                                @click="appendToExpression('1')"
                                >1</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('2')"
                                >2</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('3')"
                                >3</Button
                            >
                            <Button
                                variant="secondary"
                                @click="appendToExpression('-')"
                                >-</Button
                            >

                            <Button
                                variant="outline"
                                @click="appendToExpression('0')"
                                >0</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('.')"
                                >.</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('(')"
                                >(</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression(')')"
                                >)</Button
                            >

                            <Button
                                variant="secondary"
                                @click="appendToExpression('+')"
                                >+</Button
                            >
                            <Button
                                variant="secondary"
                                @click="appendToExpression('^')"
                                >^</Button
                            >
                            <Button
                                variant="outline"
                                @click="appendToExpression('sqrt(')"
                                >sqrt</Button
                            >
                            <Button
                                variant="destructive"
                                @click="clearExpression"
                            >
                                <X class="size-4" />
                            </Button>
                        </div>

                        <Button
                            class="w-full"
                            size="lg"
                            @click="calculate"
                            :disabled="loading"
                        >
                            Calculate
                        </Button>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle>Ticker Tape</CardTitle>
                        <Button
                            v-if="history.length > 0"
                            variant="destructive"
                            size="sm"
                            @click="clearAll"
                        >
                            <Trash2 class="mr-1 size-4" />
                            Clear All
                        </Button>
                    </CardHeader>
                    <CardContent class="flex-1 overflow-auto">
                        <div
                            v-if="history.length === 0"
                            class="py-8 text-center text-muted-foreground"
                        >
                            No calculations yet
                        </div>
                        <div v-else class="space-y-2">
                            <div
                                v-for="calc in history"
                                :key="calc.id"
                                class="group flex items-center justify-between rounded-lg bg-muted p-3"
                            >
                                <div class="font-mono">
                                    <span class="text-muted-foreground">{{
                                        calc.expression
                                    }}</span>
                                    <span class="mx-2">=</span>
                                    <span class="font-bold">{{
                                        calc.result
                                    }}</span>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    class="opacity-0 group-hover:opacity-100"
                                    @click="deleteCalculation(calc.id)"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
