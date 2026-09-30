import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, GraduationCap, IdCard, Users } from 'lucide-react';
import { useState } from 'react';
import PendingInvitationsModal from '@/components/pending-invitations-modal';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as cursosIndex } from '@/routes/cursos';
import { dashboard } from '@/routes';
import { index as fichasIndex } from '@/routes/fichas';
import type { DashboardInvitation } from '@/types';

type Props = {
    pendingInvitations?: DashboardInvitation[];
    stats: {
        totalFichas: number;
        totalCursos: number;
        fichasDelMes: number;
    };
    recentFichas: {
        id: number;
        estudiante: string;
        curso: string | null;
        fecha: string;
    }[];
};

type TeamProps = {
    currentTeam: { slug: string } | null;
};

export default function Dashboard({
    pendingInvitations = [],
    stats,
    recentFichas,
}: Props) {
    const { currentTeam } = usePage<TeamProps>().props;
    const teamSlug = currentTeam?.slug ?? '';

    const [showInvitations, setShowInvitations] = useState(
        pendingInvitations.length > 0,
    );

    return (
        <>
            <Head title="Dashboard" />

            <PendingInvitationsModal
                invitations={pendingInvitations}
                open={pendingInvitations.length > 0 && showInvitations}
                onOpenChange={setShowInvitations}
            />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-3">
                    <StatCard
                        title="Fichas registradas"
                        value={stats.totalFichas}
                        hint={`${stats.fichasDelMes} este mes`}
                        icon={IdCard}
                        href={fichasIndex(teamSlug).url}
                    />

                    <StatCard
                        title="Cursos activos"
                        value={stats.totalCursos}
                        hint="En el catálogo"
                        icon={GraduationCap}
                        href={cursosIndex(teamSlug).url}
                    />

                    <StatCard
                        title="Estado del sistema"
                        value={stats.totalFichas > 0 ? 'Activo' : 'Vacío'}
                        hint="Registra tu primera ficha para comenzar"
                        icon={Users}
                    />
                </div>

                <div className="border-sidebar-border/70 dark:border-sidebar-border flex flex-1 flex-col gap-4 overflow-hidden rounded-xl border">
                    <div className="flex items-center justify-between gap-4 p-4">
                        <div>
                            <h2 className="text-base font-medium">
                                Últimas fichas
                            </h2>
                            <p className="text-muted-foreground text-sm">
                                Matrículas registradas recientemente
                            </p>
                        </div>

                        <Button variant="outline" asChild>
                            <Link href={fichasIndex(teamSlug).url}>
                                Ver todas <ArrowRight />
                            </Link>
                        </Button>
                    </div>

                    <div className="px-4 pb-4">
                        <div className="overflow-hidden rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Estudiante</TableHead>
                                        <TableHead>Curso</TableHead>
                                        <TableHead className="text-right">
                                            Fecha
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    {recentFichas.map((ficha) => (
                                        <TableRow key={ficha.id}>
                                            <TableCell className="font-medium">
                                                {ficha.estudiante}
                                            </TableCell>

                                            <TableCell>
                                                <Badge variant="secondary">
                                                    {ficha.curso ?? '—'}
                                                </Badge>
                                            </TableCell>

                                            <TableCell className="text-muted-foreground text-right">
                                                {ficha.fecha}
                                            </TableCell>
                                        </TableRow>
                                    ))}

                                    {recentFichas.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={3}
                                                className="text-muted-foreground h-24 text-center"
                                            >
                                                Todavía no hay fichas
                                                registradas.
                                            </TableCell>
                                        </TableRow>
                                    ) : null}
                                </TableBody>
                            </Table>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

function StatCard({
    title,
    value,
    hint,
    icon: Icon,
    href,
}: {
    title: string;
    value: number | string;
    hint: string;
    icon: typeof IdCard;
    href?: string;
}) {
    const content = (
        <div className="border-sidebar-border/70 dark:border-sidebar-border flex items-start justify-between gap-4 rounded-xl border p-4">
            <div className="space-y-1">
                <p className="text-muted-foreground text-sm">{title}</p>
                <p className="text-2xl font-semibold">{value}</p>
                <p className="text-muted-foreground text-xs">{hint}</p>
            </div>

            <div className="bg-primary/10 text-primary rounded-lg p-2">
                <Icon className="size-5" />
            </div>
        </div>
    );

    return href ? (
        <Link href={href} className="transition-opacity hover:opacity-80">
            {content}
        </Link>
    ) : (
        content
    );
}

Dashboard.layout = (props: TeamProps) => ({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
        },
    ],
});
