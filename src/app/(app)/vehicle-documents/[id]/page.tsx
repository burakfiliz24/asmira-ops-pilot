import Link from "next/link";
import { buttonClasses } from "@/components/ui/Button";

export default async function VehicleDocumentDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Araç Evrakı</h1>
        <p className="mt-1 text-sm opacity-70">Kayıt: {id}</p>
      </div>

      <div className="flex flex-wrap gap-2">
        <Link
          href={`/vehicle-documents/${id}/edit`}
          className={buttonClasses({ variant: "primary", size: "md" })}
        >
          Düzenle
        </Link>
        <Link
          href="/vehicle-documents"
          className={buttonClasses({ variant: "secondary", size: "md" })}
        >
          Listeye Dön
        </Link>
      </div>
    </div>
  );
}
