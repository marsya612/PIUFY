use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('piutangs', function (Blueprint $table) {
            $table->boolean('reminder_7_sent')->default(false);
            $table->boolean('reminder_5_sent')->default(false);
            $table->boolean('reminder_3_sent')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('piutangs', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_7_sent',
                'reminder_5_sent',
                'reminder_3_sent'
            ]);
        });
    }
};
