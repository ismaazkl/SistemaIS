import { Form } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Method } from '@inertiajs/core';

type Props = {
    action: { url: string; method: Method };
    title: string;
    description: string;
    confirmLabel?: string;
    /** Invoked when the request finishes; use it to reset local state. */
    onFinished?: () => void;
    children: React.ReactNode;
};

export default function ConfirmDialog({
    action,
    title,
    description,
    confirmLabel = 'Eliminar',
    onFinished,
    children,
}: Props) {
    const [open, setOpen] = useState(false);

    return (
        <>
            <span
                className="inline-flex"
                onClick={(event) => {
                    event.preventDefault();
                    setOpen(true);
                }}
            >
                {children}
            </span>

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle className="flex items-center gap-2">
                            <AlertTriangle className="text-destructive size-5" />
                            {title}
                        </DialogTitle>
                        <DialogDescription>{description}</DialogDescription>
                    </DialogHeader>

                    <Form
                        action={action}
                        options={{ preserveScroll: true }}
                        onSuccess={() => {
                            setOpen(false);
                            onFinished?.();
                        }}
                    >
                        {({ processing, errors }) => (
                            <>
                                {errors.curso ? (
                                    <p className="text-destructive text-sm">
                                        {errors.curso}
                                    </p>
                                ) : null}

                                <DialogFooter>
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        onClick={() => setOpen(false)}
                                    >
                                        Cancelar
                                    </Button>

                                    <Button type="submit" disabled={processing}>
                                        {processing
                                            ? 'Eliminando...'
                                            : confirmLabel}
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>
        </>
    );
}
