import Link from "next/link";
import { buttonClasses } from "@/components/ui/Button";

export default async function EditVehicleDocumentPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Araç Evrakı Düzenle</h1>
        <p className="mt-1 text-sm opacity-70">Kayıt: {id}</p>
      </div>

      <div>
        <Link
          href="/vehicle-documents"
          className={buttonClasses({ variant: "secondary", size: "md" })}
        >
          İptal
        </Link>
      </div>
    </div>
  );
}
