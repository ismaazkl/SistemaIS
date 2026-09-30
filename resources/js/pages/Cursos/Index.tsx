import { Form, Head, router, usePage } from '@inertiajs/react';
import { Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import ConfirmDialog from '@/components/confirm-dialog';
import CursoController from '@/actions/App/Http/Controllers/CursoController';
import CursoFormFields from '@/components/curso-form-fields';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as cursosIndex } from '@/routes/cursos';
import type { Curso, CursoFilters } from '@/types';

type Props = {
    cursos: Curso[];
    filters: CursoFilters;
    canManage: boolean;
};

type TeamProps = {
    currentTeam: { slug: string } | null;
};

export default function CursosIndex({ cursos, filters, canManage }: Props) {
    const { currentTeam } = usePage<TeamProps>().props;
    const teamSlug = currentTeam?.slug ?? '';

    const [search, setSearch] = useState(filters.search);
    const [createOpen, setCreateOpen] = useState(false);
    const [editing, setEditing] = useState<Curso | null>(null);

    useEffect(() => {
        if (search === filters.search) {
            return;
        }

        const timeout = setTimeout(() => {
            router.get(
                cursosIndex(teamSlug),
                { search: search || undefined },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        }, 300);

        return () => clearTimeout(timeout);
    }, [search, filters.search, teamSlug]);

    const submitSearch = (event: React.FormEvent) => {
        event.preventDefault();

        router.get(
            cursosIndex(teamSlug),
            { search: search || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    return (
        <>
            <Head title="Cursos" />

            <h1 className="sr-only">Cursos</h1>

            <div className="space-y-6">
                <div className="flex items-center justify-between gap-4">
                    <Heading
                        variant="small"
                        title="Cursos"
                        description="Catálogo de cursos del colegio"
                    />

                    {canManage ? (
                        <Button onClick={() => setCreateOpen(true)}>
                            <Plus /> Nuevo curso
                        </Button>
                    ) : null}
                </div>

                <form onSubmit={submitSearch} className="flex gap-2">
                    <div className="relative flex-1">
                        <Search className="text-muted-foreground pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2" />
                        <Input
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Buscar curso..."
                            className="pl-9"
                            aria-label="Buscar curso"
                        />
                    </div>

                    {search !== filters.search ? <Spinner /> : null}
                </form>

                <div className="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10">#</TableHead>
                                <TableHead>Curso</TableHead>
                                <TableHead className="w-32 text-right">
                                    Fichas
                                </TableHead>
                                {canManage ? (
                                    <TableHead className="w-24 text-right">
                                        Acciones
                                    </TableHead>
                                ) : null}
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {cursos.map((curso, index) => (
                                <TableRow key={curso.id}>
                                    <TableCell className="text-muted-foreground">
                                        {index + 1}
                                    </TableCell>

                                    <TableCell className="font-medium">
                                        {curso.curso}
                                    </TableCell>

                                    <TableCell className="text-right">
                                        <Badge
                                            variant={
                                                curso.fichasCount > 0
                                                    ? 'default'
                                                    : 'secondary'
                                            }
                                        >
                                            {curso.fichasCount}
                                        </Badge>
                                    </TableCell>

                                    {canManage ? (
                                        <TableCell>
                                            <div className="flex items-center justify-end gap-1">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label={`Editar ${curso.curso}`}
                                                    onClick={() =>
                                                        setEditing(curso)
                                                    }
                                                >
                                                    <Pencil />
                                                </Button>

                                                <ConfirmDialog
                                                    action={CursoController.destroy(
                                                        {
                                                            current_team:
                                                                teamSlug,
                                                            curso: curso.id,
                                                        },
                                                    )}
                                                    title="Eliminar curso"
                                                    description={`¿Seguro que deseas eliminar "${curso.curso}"? Esta acción no se puede deshacer.`}
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        aria-label={`Eliminar ${curso.curso}`}
                                                        className="text-destructive hover:text-destructive"
                                                    >
                                                        <Trash2 />
                                                    </Button>
                                                </ConfirmDialog>
                                            </div>
                                        </TableCell>
                                    ) : null}
                                </TableRow>
                            ))}

                            {cursos.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={canManage ? 4 : 3}
                                        className="text-muted-foreground h-24 text-center"
                                    >
                                        No hay cursos para mostrar.
                                    </TableCell>
                                </TableRow>
                            ) : null}
                        </TableBody>
                    </Table>
                </div>
            </div>

            <Dialog open={createOpen} onOpenChange={setCreateOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Nuevo curso</DialogTitle>
                        <DialogDescription>
                            Registra un curso nuevo en el catálogo.
                        </DialogDescription>
                    </DialogHeader>

                    <Form
                        action={CursoController.store(teamSlug)}
                        options={{ preserveScroll: true }}
                        resetOnError
                        onSuccess={() => setCreateOpen(false)}
                    >
                        {({ processing, errors }) => (
                            <>
                                <CursoFormFields
                                    error={errors.curso}
                                    autoFocus
                                />

                                <DialogFooter>
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        onClick={() => setCreateOpen(false)}
                                    >
                                        Cancelar
                                    </Button>

                                    <Button type="submit" disabled={processing}>
                                        {processing
                                            ? 'Guardando...'
                                            : 'Guardar curso'}
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>

            <Dialog
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Editar curso</DialogTitle>
                        <DialogDescription>
                            Actualiza el nombre del curso.
                        </DialogDescription>
                    </DialogHeader>

                    {editing ? (
                        <Form
                            action={CursoController.update({
                                current_team: teamSlug,
                                curso: editing.id,
                            })}
                            options={{ preserveScroll: true }}
                            resetOnError
                            onSuccess={() => setEditing(null)}
                        >
                            {({ processing, errors }) => (
                                <>
                                    <CursoFormFields
                                        defaultValue={editing.curso}
                                        error={errors.curso}
                                        autoFocus
                                    />

                                    <DialogFooter>
                                        <Button
                                            type="button"
                                            variant="secondary"
                                            onClick={() => setEditing(null)}
                                        >
                                            Cancelar
                                        </Button>

                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            {processing
                                                ? 'Guardando...'
                                                : 'Actualizar'}
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    ) : null}
                </DialogContent>
            </Dialog>
        </>
    );
}

CursosIndex.layout = (props: TeamProps) => ({
    breadcrumbs: [
        {
            title: 'Cursos',
            href: props.currentTeam ? cursosIndex(props.currentTeam.slug) : '/',
        },
    ],
});
