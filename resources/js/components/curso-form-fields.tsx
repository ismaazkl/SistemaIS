import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    /** Field name used by the form. */
    name?: string;
    defaultValue?: string;
    error?: string;
    autoFocus?: boolean;
};

export default function CursoFormFields({
    name = 'curso',
    defaultValue = '',
    error,
    autoFocus,
}: Props) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={name}>Curso</Label>

            <Input
                id={name}
                name={name}
                defaultValue={defaultValue}
                placeholder="Ej. 1ERO EGB A"
                maxLength={120}
                required
                autoFocus={autoFocus}
                autoComplete="off"
            />

            <InputError message={error} className="mt-2" />
        </div>
    );
}
